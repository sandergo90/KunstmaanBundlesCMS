<?php

namespace Kunstmaan\CookieBundle\Form;

use Kunstmaan\AdminBundle\Form\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CookieTypeAdminType
 *
 * @package Kunstmaan\CookieBundle\Form
 */
class CookieTypeAdminType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'required' => true,
                'label' => 'kuma.cookie.adminlists.cookie_type.name',
            ]
        );
        $builder->add(
            'shortDescription',
            WysiwygType::class,
            [
                'required' => false,
                'label' => 'kuma.cookie.adminlists.cookie_type.short_description',
                'attr' => [
                    'height' => 100,
                ]
            ]
        );
        $builder->add(
            'longDescription',
            WysiwygType::class,
            [
                'required' => false,
                'label' => 'kuma.cookie.adminlists.cookie_type.long_description',
            ]
        );
        $builder->add(
            'internalName',
            TextType::class,
            [
                'required' => true,
                'label' => 'kuma.cookie.adminlists.cookie_type.internal_name_with_explanation',
            ]
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'kunstmaancookiebundle_cookietype_form';
    }
}
