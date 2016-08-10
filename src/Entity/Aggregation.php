<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Entity;

/**
 * class  Aggregation
 * @author Tomfun <tomfun1990@gmail.com>
 */
class Aggregation
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $type;
    /** @var string|null */
    protected $setter = null;
    /** @var string|null */
    protected $index = null;
    /** @var mixed|null */
    protected $extra = null;


    /**
     * @param string $name
     * @param string $type
     * @param string|null $index
     * @param string|null $setter
     * @param mixed|null $extra
     */
    public function __construct($name, $type, $index = null, $setter = null, $extra = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->index = $index;
        $this->setter = $setter;
        $this->extra = array_merge(['extractValueField' => 'value'], $extra);
    }

    /**
     * @return string
     */
    public function getSetter()
    {
        if ($this->setter) {
            return $this->setter;
        }
        $r = 'set';
        foreach (explode('_', $this->name) as $s) {
            $r .= ucfirst($s);
        }
        return $r;
    }

    /**
     * @param string $setter
     * @return $this
     */
    public function setSetter($setter)
    {
        $this->setter = $setter;
        return $this;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        if ($this->index) {
            return $this->index;
        }
        return $this->name;
    }

    /**
     * @param string $index
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param mixed $extra
     * @return $this
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
        return $this;
    }
}