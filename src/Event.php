<?php

declare(strict_types=1);

namespace WPZinc\Airwallex;

/**
 * A webhook event.
 *
 * @property string                $id
 * @property string                $name       Event type, e.g. payment_intent.succeeded.
 * @property string|null           $account_id
 * @property string|null           $org_id
 * @property AirwallexObject       $data       The resource is in $event->data->object.
 * @property string                $created_at
 * @property string                $version    API version used to render data.
 */
class Event extends AirwallexObject
{
    /**
     * The resource the event is about.
     */
    public function object(): ?AirwallexObject
    {
        $data = $this->values['data'] ?? null;

        return $data instanceof AirwallexObject && $data->object instanceof AirwallexObject ? $data->object : null;
    }

    /**
     * Event type, e.g. "payment_intent.succeeded".
     */
    public function type(): string
    {
        return (string) ($this->values['name'] ?? '');
    }
}
