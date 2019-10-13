<?php declare(strict_types = 1);

namespace App\Entity;

use App\Model;
use App\Utils\Doctrine\BaseFields;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="exchange")
 * @ORM\HasLifecycleCallbacks()
 */
class Exchange extends Model\Exchange
{
    use BaseFields;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Rate", mappedBy="exchange", fetch="LAZY")
     */
    private $rates;

    public function __construct()
    {
        $this->rates = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getRates(): ArrayCollection
    {
        return $this->rates;
    }

    /**
     * @param ArrayCollection $rates
     *
     * @return Exchange
     */
    public function setRates(ArrayCollection $rates): Exchange
    {
        $this->rates = $rates;

        return $this;
    }

    /**
     * @param Rate $rate
     *
     * @return Exchange
     */
    public function addRate(Rate $rate): Exchange
    {
        $this->rates->add($rate);

        return $this;
    }
}
