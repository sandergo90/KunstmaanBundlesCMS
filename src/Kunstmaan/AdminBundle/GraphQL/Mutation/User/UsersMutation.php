<?php

namespace Kunstmaan\AdminBundle\GraphQL\Mutation\User;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\GraphQL\Type\UserMutationType;
use Kunstmaan\AdminBundle\GraphQL\Type\UserType;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

/**
 * Class UsersMutation.
 */
class UsersMutation extends AbstractContainerAwareField
{
    public function getName()
    {
        return 'users';
    }

    public function build(FieldConfig $config)
    {
        $config
            ->addArguments([
                'user' => new UserMutationType()
            ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $container = $info->getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');
        $userFields = $args['user'];

        $user = $em->getRepository('KunstmaanAdminBundle:User')->findOneBy([
            'username' => $userFields['username']
        ]);

        if (!$user) {
            $user = new User();
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($userFields as $name => $value) {
            if ($accessor->isWritable($user,$name)) {
                $accessor->setValue($user, $name, $value);
            }
        }

        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new UserType();
    }
}