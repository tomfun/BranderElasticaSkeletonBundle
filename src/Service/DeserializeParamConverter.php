<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Service;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\Exception;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @example ParamConverter("filters", options={"deserialization_groups": "Default"}, converter="query_deserializer")
 * @author Tomfun <tomfun1990@gmail.com>
 */
class DeserializeParamConverter implements
    ParamConverterInterface
{
    const SUPPORT_CLASS = 'Brander\\Bundle\\ElasticaSkeletonBundle\\Service\\Elastica\\ElasticaQuery';
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        if (!($class = $configuration->getClass())) {
            return false;
        }

        return self::SUPPORT_CLASS === $class || is_subclass_of($class, self::SUPPORT_CLASS);
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $class = $configuration->getClass();
        $options = $configuration->getOptions();
        //$alias = $configuration->getAliasName();
        $context = null;
        if (isset($options['deserialization_groups'])) {
            $context = DeserializationContext::create()->setGroups($options['deserialization_groups']);
        }
        try {
            $object = $this->serializer->deserialize(
                $request->get($configuration->getName()),
                $class,
                'json',
                $context
            );
        } catch (Exception $e) {
            throw new NotFoundHttpException(
                sprintf(
                    'Could not deserialize request content to object of type "%s": ' . $e->getMessage(),
                    $class
                )
            );
        }

        // set the object as the request attribute with the given name
        // (this will later be an argument for the action)
        $request->attributes->set($configuration->getName(), $object);

        return true;
    }
}