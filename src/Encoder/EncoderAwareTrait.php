<?php

namespace Qu\Encoder;

trait EncoderAwareTrait
{
    /**
     * @var EncoderInterface
     */
    protected $encoder;

    /**
     * @param EncoderInterface $serializer
     * @return $this
     */
    public function setEncoder(EncoderInterface $serializer)
    {
        $this->encoder = $serializer;

        return $this;
    }

    /**
     * @return EncoderInterface
     */
    public function getEncoder()
    {
        if (null === $this->encoder) {
            $this->encoder = new JsonEncoder;
        }
        return $this->encoder;
    }
}