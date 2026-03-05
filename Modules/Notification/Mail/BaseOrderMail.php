<?php

namespace Modules\Notification\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Order\Models\Order;

abstract class BaseOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    final public function build(): self
    {
        return $this->subject($this->getSubject())
            ->view('notification::emails.order')
            ->with([
                'order' => $this->order,
                'greeting' => $this->getGreeting(),
                'body' => $this->getBody(),
                'actionText' => $this->getActionText(),
                'actionUrl' => $this->getActionUrl(),
            ]);
    }
    abstract protected function getSubject(): string;
    abstract protected function getBody(): string;
    abstract protected function getActionText(): string;
    abstract protected function getActionUrl(): string;

    protected function getGreeting(): string
    {
        return 'Hello, ' . $this->order->user->name . '!';
    }
}
