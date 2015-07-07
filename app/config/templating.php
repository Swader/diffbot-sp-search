<?php

class TemplateConfigurator
{

    const TWIG = 1;
    const HTPL = 2;

    const TWIG_TEMPLATES = 'twig';
    const HTPL_TEMPLATES = 'htpl';

    /** @var string */
    protected $engineFlag = '';
    /** @var Twig_Environment */
    protected $engineInstance;

    /** @var array */
    protected $vars;

    public function __construct($engine, $debug = true)
    {
        $this->engineFlag = $engine;
        switch ($engine) {
            case self::TWIG:
                $this->engineInstance = $this->configureTwig($debug);
                break;
            case self::HTPL:
                $this->engineInstance = $this->configureHtpl($debug);
                break;
            default:
                $this->error();
        }
    }

    protected function configureTwig($debug)
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../../template');
        $twig = new Twig_Environment($loader
//   , array('cache' => __DIR__ . '/../cache',)
            , array('cache' => false, 'debug' => $debug)
        );

        $function = new Twig_SimpleFunction('qprw',
            function (array $replacements) {
                parse_str($_SERVER['QUERY_STRING'], $qp);
                foreach ($replacements as $k => $v) {
                    $qp[$k] = $v;
                }

                return '?' . http_build_query($qp);
            });
        $twig->addFunction($function);

        return $twig;
    }

    protected function configureHtpl($debug)
    {
        $provider = new \Webiny\Htpl\TemplateProviders\FilesystemProvider([__DIR__ . '/../../template']);
        $cache = new \Webiny\Htpl\Cache\FilesystemCache(__DIR__ . '/../../cache');

        $htpl = new \Webiny\Htpl\Htpl($provider, $cache);

        return $htpl;
    }

    public function render($template)
    {
        switch ($this->engineFlag) {
            case self::TWIG:
                $template = self::TWIG_TEMPLATES . '/' . $template . '.twig';
                echo $this->engineInstance->render($template, $this->vars);
                break;
            case self::HTPL:
                $template = self::HTPL_TEMPLATES . '/' . $template . '.htpl';
                $this->engineInstance->assignArray($this->vars);
                $this->engineInstance->display($template);
                break;
            default:
                $this->error();
        }
    }

    public function set($key, $value)
    {
        $this->vars[$key] = $value;

        return $this->engineInstance;
    }

    public function setAll(array $array)
    {
        foreach ($array as $k => $v) {
            $this->vars[$k] = $v;
        }

        return $this->engineInstance;
    }

    protected function error()
    {
        throw new \Exception('No template engine selected!');
    }
}