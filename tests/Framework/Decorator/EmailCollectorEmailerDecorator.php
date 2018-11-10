<?php

declare(strict_types=1);

/*
 * This file is part of my Symfony boilerplate,
 * following the Explicit Architecture principles.
 *
 * @link https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together
 * @link https://herbertograca.com/2018/07/07/more-than-concentric-layers/
 *
 * (c) Herberto Graça
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\App\Test\Framework\Decorator;

use Acme\App\Core\Port\Notification\Client\Email\Email;
use Acme\App\Core\Port\Notification\Client\Email\EmailerInterface;

final class EmailCollectorEmailerDecorator implements EmailerInterface
{
    /**
     * @var EmailerInterface
     */
    private $emailer;

    /**
     * @var Email[]
     */
    private $sentEmailList = [];

    public function __construct(EmailerInterface $emailer)
    {
        $this->emailer = $emailer;
    }

    public function send(Email $email): void
    {
        $this->emailer->send($email);

        $this->sentEmailList[] = $email;
    }

    /**
     * @return Email[]
     */
    public function getSentEmails(): array
    {
        return $this->sentEmailList;
    }
}
