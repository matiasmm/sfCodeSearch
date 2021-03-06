<?php

namespace Symfony\Bundle\TwigBundle;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This engine knows how to render Twig templates.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class TwigEngine implements EngineInterface
{
    protected $environment;
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container   The DI container
     * @param \Twig_Environment  $environment A \Twig_Environment instance
     * @param GlobalVariables    $globals     A GlobalVariables instance
     */
    public function __construct(ContainerInterface $container, \Twig_Environment $environment, GlobalVariables $globals)
    {
        $this->container = $container;
        $this->environment = $environment;

        $environment->addGlobal('app', $globals);
    }

    /**
     * Renders a template.
     *
     * @param string $name       A template name
     * @param array  $parameters An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     *
     * @throws \InvalidArgumentException if the template does not exist
     * @throws \RuntimeException         if the template cannot be rendered
     */
    public function render($name, array $parameters = array())
    {
        return $this->load($name)->render($parameters);
    }

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return Boolean true if the template exists, false otherwise
     */
    public function exists($name)
    {
        try {
            $this->load($name);
        } catch (\Twig_Error_Loader $e) {
            return false;
        }

        return true;
    }

    /**
     * Loads the given template.
     *
     * @param string $name A template name
     *
     * @return \Twig_TemplateInterface A \Twig_TemplateInterface instance
     *
     * @throws \Twig_Error_Loader if the template cannot be found
     */
    public function load($name)
    {
        return $this->environment->loadTemplate($name);
    }

    /**
     * Returns true if this class is able to render the given template.
     *
     * @param string $name A template name
     *
     * @return boolean True if this class supports the given resource, false otherwise
     */
    public function supports($name)
    {
        return false !== strpos($name, '.twig');
    }

    /**
     * Renders a view and returns a Response.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A Response instance
     *
     * @return Response A Response instance
     */
    public function renderResponse($view, array $parameters = array(), Response $response = null)
    {
        if (null === $response) {
            $response = $this->container->get('response');
        }

        $response->setContent($this->render($view, $parameters));

        return $response;
    }
}
