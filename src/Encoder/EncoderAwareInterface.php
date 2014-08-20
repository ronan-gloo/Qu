<?php

namespace Qu\Encoder;

interface EncoderAwareInterface
{
    /**
     * @param EncoderInterface $serializer
     * @return self
     */
    public function setEncoder(EncoderInterface $serializer);

    /**
     * @return EncoderInterface
     */
    public function getEncoder();
}