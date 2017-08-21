<?php

namespace Orion\Context;

/**
 * Class to abstract NGSIv2 Subscriptions
 * ref. https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv2/index.html#subscriptions
 * @author Leonan
 */
class SubscriptionFactory {
    
    const endPoint = "subscriptions";
    /**
     * Subject Object
     * @var \stdClass
     */
    public $_subscription;
    /**
     * Orion NGSI Connection interface V2
     * @var \Orion\NGSIAPIv2
     */
    private $_orion;

    public function __construct(\Orion\NGSIAPIv2 $orion = null, $description = null, $expires = null) {
        $this->_orion = $orion;
        $this->_subscription = (object) [];
        if (null != $expires) {
            $this->setExpiration($expires);
        }
        
        if(null != $description){
            $this->setDescription($description);
        }
    }
    
    /**
     * Set date in ISO8601 format to subscription object
     * "Subscriptions may have an expiration date (expires field),
     * specified using the ISO 8601 standard format.
     * Once subscription overpass that date, the subscription is simply ignored
     * (however, it is still stored in the broker database and needs to be purged
     *  using the procedure described in the administration manual).
     * You can extend the duration of a subscription by updating it,
     *  as described in this document[https://fiware-orion.readthedocs.io/en/develop/user/duration/index.html#extending-duration]"
     * @param string|time $expire_time any supported type of date ref.: http://php.net/manual/en/datetime.construct.php
     * @param string  http://php.net/manual/en/class.datetimezone.php
     * @return \Orion\Context\Subscription
     */
    public function setExpiration($expire_time, $timezone = "UTC") {
        //It's not possible construct DateTime class with unix timestamp
        if (is_int($expire_time) && $this->isValidTimeStamp($expire_time)) {
            $date = new \DateTime();
            $date->setTimestamp($expire_time);
        } else {
            $date = new \DateTime($expire_time);
        }

        $date->setTimezone(new \DateTimeZone((string)$timezone));

        $this->_subscription->expires = $date->format(\DateTime::ISO8601);
        return $this;
    }

    private function isValidTimeStamp($timestamp)
    {
        return ($timestamp <= PHP_INT_MAX) && ($timestamp >= ~PHP_INT_MAX);
    }

    /**
     * Set description from subscription
     * @param type $description
     */
    public function setDescription($description)
    {
        $this->_subscription->description = $description;
    }

    /**
     * Add a Entity to subject.entities
     * @param string $id
     * @param string $type
     * @param string $idKey "id"(default) or "idPattern"
     * @param string $typeKey "type"(default) or "typePattern"
     * @return \Orion\Context\Subscription
     */
    public function addEntitySubject($id, $type = null, $idKey = "id", $typeKey = "type")
    {
        if(!isset($this->_subscription->subject)){
            $this->_subscription->subject = (object) [
                "entities" => [],
                "condition" => (object)["attrs" => []]
            ];
        }

        $entity = (object) [];
        $entity->$idKey = $id;

        if (null != $type) {
            $entity->$typeKey = (string)$type;
        }

        $this->_subscription->subject->entities[] = $entity;
        return $this;
    }

    /**
     * Add a Attr to subject.condition.attr
     *
     * "The conditions element defines the "trigger" for the subscription.
     * The  attrs field contains a list of attribute names.
     * These names define the "triggering attributes",
     * i.e. attributes that upon creation/change due to entity creation or
     * update trigger the notification."
     *
     * "The rule is that if at least one of the attributes in the conditions.attrs
     * list changes (e.g. some kind of "OR" condition), then a notification is sent. "
     *
     * "You can leave conditions.attrs empty to make a notification trigger on
     * any entity attribute change (regardless of the name of the attribute)."
     *
     * "You can include filtering expressions in conditions.
     * For example, to get notified not only if pressure changes,
     *  but if it changes within the range 700-800.
     * This is an advanced topic, see the "Subscriptions" section in the NGSIv2 specification.
     * [http://telefonicaid.github.io/fiware-orion/api/v2/stable/]"
     *
     * @param string $id
     * @param string $type
     * @return \Orion\Context\Subscription
     */
    public function addAttrCondition($attr) {
        if(!isset($this->_subscription->subject)){
            $this->_subscription->subject = (object) [];
        }

        if(!isset($this->_subscription->subject->condition)){
            $this->_subscription->subject->condition = (object) [];
        }

        if (!isset($this->_subscription->subject->condition->attrs)) {
            $this->_subscription->subject->condition->attrs = [];
        }
        array_push($this->_subscription->subject->condition->attrs, $attr);
        return $this;
    }

    /**
     * Add expression condition
     * " an expression composed of `q`,`georel`,`geometry`  and `coords`
     *
     * http://telefonicaid.github.io/fiware-orion/api/v2/stable/
     * @param string $expression
     * @return \Orion\Context\Subscription
     */
    public function addExpressionCondition($expression) {
        if(!isset($this->_subscription->subject)){
            $this->_subscription->subject = (object) [];
        }

        if(!isset($this->_subscription->subject->condition)){
            $this->_subscription->subject->condition = (object) [];
        }


        $this->_subscription->subject->condition->expression = $expression;
        return $this;
    }

    /**
     * Set Notification URL.
     * "The URL where to send notifications is defined with the  url sub-field.
     * Only one URL can be included per subscription.
     *  However, you can have several subscriptions on the same context elements
     * (i.e. same entity and attribute) without any problem."
     * @param type $url  URL referencing the service to be invoked when a notification is generated. An NGSIv2 compliant server must support the  URL schema. Other schemas could also be supported.
     * @param string $schema http or httpCustom
     * @param type $qs  (optional): a key-map of URI queryString parameters that are included in notification messages
     * @param array $headers  (optional): a key-map of HTTP headers that are included in notification messages.
     * @param type $method  (optional): the method to use when sending the notification (default is POST). Only valid HTTP methods are allowed. On specifying an invalid HTTP method, a 400 Bad Request error is returned.
     * @param type $payload (optional): the payload to be used in notifications. If omitted, the default payload (see "Notification Messages" sections on http://telefonicaid.github.io/fiware-orion/api/v2/stable/) is used.
     * @return \Orion\Context\Subscription
     */
    public function setNotificationURL($url, $schema = "http", $qs = null, array $headers = null, $method = "POST", $payload = null) {
        if(!isset($this->_subscription->notification)){
            $this->_subscription->notification = (object) [];
        }

        $this->_subscription->notification->$schema = (object) [];
        //You can't send both at the same time, It is used to covery parameters for notifications delivered through the http protocol.
        if ($schema == "http") {
            if (isset($this->_subscription->notification->httpCustom)) {
                unset($this->_subscription->notification->httpCustom);
            }
        } elseif ($schema == "httpCustom") {
            /**
            * <code>"httpCustom": {
              * "url": "http://foo.com/entity/${id}",
              * "headers": {
                * "Content-Type": "text/plain"
              * },
              * "method": "PUT",
              * "qs": {
                * "type": "${type}"
              * },
              * "payload": "The temperature is ${temperature} degrees"
            * }
            * </code>
            * will send this request
            * <code>PUT http://foo.com/entity/DC_S1-D41?type=Room
            * Content-Type: text/plain
             * Content-Length: 31
 *
* The temperature is 23.4 degrees
            * </code>
            **/
            if (isset($this->_subscription->notification->http)) {
                unset($this->_subscription->notification->http);
            }

            //a key-map of HTTP headers that are included in notification messages
            if (null != $qs) {
                $this->_subscription->notification->httpCustom->qs = $qs;
            }
            //a key-map of HTTP headers that are included in notification messages
            if (null != $headers) {
                $this->_subscription->notification->httpCustom->headers = $headers;
            }
            //the method to use when sending the notification (default is POST).
            $this->_subscription->notification->httpCustom->method = $method;

            //the payload to be used in notification. If omitted, the default paload is used
            if (null != $payload) {
                $this->_subscription->notification->httpCustom->payload = $payload;
            }
        }

        $this->_subscription->notification->$schema->url = $url;
        return $this;
    }

    /**
     * Set attribute list from notification
     * "List of attributes to be included in notification messages."
     * @param string $attr
     * @return \Orion\Context\Subscription
     */
    public function addNotificationAttr($attr) {
        if(!isset($this->_subscription->notification)){
            $this->_subscription->notification = (object) [];
        }

        if (!isset($this->_subscription->notification->attrs)) {
            $this->_subscription->notification->attrs = [];
        }
        array_push($this->_subscription->notification->attrs, $attr);
        return $this;
    }

    /**
     * Set attribute list from notification
     * "List of attributes to be included in notification messages."
     * @param string $exceptAttr
     * @return \Orion\Context\Subscription
     */
    public function addNotificationExceptAttr($exceptAttr) {
        if(!isset($this->_subscription->notification)){
            $this->_subscription->notification = (object) [];
        }

        if (!isset($this->_subscription->notification->exceptAttrs)) {
            $this->_subscription->notification->exceptAttrs = [];
        }
        array_push($this->_subscription->notification->exceptAttrs, $exceptAttr);
        return $this;
    }

    /**
     * Set attr format.
     * attrsFormat(optional): specifies how the entities are represented in notifications.
     * Accepted values:`normalized`(default), `keyValues` or `values`
     * @param string $format
     * @return \Orion\Context\Subscription
     */
    public function setNotificationAttrFormat($format = "normalized") {
        if(!isset($this->_subscription->notification)){
            $this->_subscription->notification = (object) [];
        }

        $possibleValues = ["normalized", "keyValues", "values"];
        if (!in_array($possibleValues, $format)) {
            throw new \Exception("Not supported format");
        }
        $this->_subscription->notification->attrsFormat = $format;
        return $this;
    }

    /**
     * Set throttling value
     *
     * "The throttling element is used to specify a minimum inter-notification arrival time.
     * So, setting throttling to 5 seconds as in the example above,
     * makes a notification not to be sent if a previous notification was sent less than 5 seconds ago,
     *  no matter how many actual changes take place in that period.
     * This is to give the notification receptor a means to protect itself
     *  against context producers that update attribute values too frequently.
     * In multi-CB configurations, take into account that the last-notification
     * measure is local to each CB node. Although each node periodically
     * synchronizes with the DB in order to get potencially newer values
     * (more on this here it may happen that a particular node has an old value,
     *  so throttling is not 100% accurate.)"
     * @param int $throttling throttling in secconds
     * @return \Orion\Context\Subscription
     */
    public function setThrottling($throttling) {
        $this->_subscription->throttling = $throttling;
        return $this;
    }

    public function prettyPrint() {
        echo json_encode($this->_subscription, JSON_PRETTY_PRINT);
    }
    /**
     * 
     * @return \Orion\Context\SubscriptionEntity
     */
    public function create(&$request = null){
        //TODO:-> Validate Required fields
        
        
        $request = $this->_orion->post(self::endPoint, $this->_subscription);
        $retInfo = $request->getResponseInfo();
        
        if (is_array($retInfo) && array_key_exists("http_code", $retInfo) && $retInfo['http_code'] == 201 //Te http request has executed with success
        ) {
            $pieces = explode("/", $request->getResponseHeader("Location"));
            $subscriptionID = end($pieces);
            return new SubscriptionEntity($this->_orion,$subscriptionID);
        }
        
        $responseContext = new Context($request->getResponseBody());
        if (isset($responseContext->get()->error)) {
            $errorResponse = $responseContext->get();
            $exception_name = "\\Orion\\Exception\\{$errorResponse->error}";
            if (class_exists($exception_name)) {
                throw new $exception_name($errorResponse->description, 500, null, $request);
            } else {
                $restReq = null;
                throw new \Orion\Exception\GeneralException($errorResponse->error . " : " . $errorResponse->description, 500, null, $restReq);
            }
        }
        
    }
    
    public function get(){
        return $this->_subscription;
    }
}
