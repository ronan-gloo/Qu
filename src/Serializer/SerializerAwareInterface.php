<?php

namespace Qu\Serializer;

interface SerializerAwareInterface
{
    /**
     * @param SerializerInterface $serializer
     * @return self
     */
    public function setSerializer(SerializerInterface $serializer);

    /**
     * @return SerializerInterface
     */
    public function getSerializer();
}