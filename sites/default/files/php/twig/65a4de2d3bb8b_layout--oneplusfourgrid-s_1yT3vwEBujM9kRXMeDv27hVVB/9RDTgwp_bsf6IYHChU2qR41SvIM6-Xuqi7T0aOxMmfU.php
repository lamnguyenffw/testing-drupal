<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* core/profiles/demo_umami/themes/umami/layouts/oneplusfourgrid_section/layout--oneplusfourgrid-section.html.twig */
class __TwigTemplate_5753ac56cd2a612574589497eeb71c36 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 15
        $context["classes"] = ["layout", "layout--oneplusfourgrid-section"];
        // line 20
        if (($context["content"] ?? null)) {
            // line 21
            echo "  <div";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [($context["classes"] ?? null)], "method", false, false, true, 21), 21, $this->source), "html", null, true);
            echo ">

    ";
            // line 23
            if (twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "first", [], "any", false, false, true, 23)) {
                // line 24
                echo "      <div";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["region_attributes"] ?? null), "first", [], "any", false, false, true, 24), "addClass", ["layout__region", "layout__region--first"], "method", false, false, true, 24), 24, $this->source), "html", null, true);
                echo ">
        ";
                // line 25
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "first", [], "any", false, false, true, 25), 25, $this->source), "html", null, true);
                echo "
      </div>
    ";
            }
            // line 28
            echo "
    ";
            // line 29
            if ((((twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "second", [], "any", false, false, true, 29) || twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "third", [], "any", false, false, true, 29)) || twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "fourth", [], "any", false, false, true, 29)) || twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "fifth", [], "any", false, false, true, 29))) {
                // line 30
                echo "      <div class=\"layout__four-grid-group\">
    ";
            }
            // line 32
            echo "
      ";
            // line 33
            if (twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "second", [], "any", false, false, true, 33)) {
                // line 34
                echo "        <div";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["region_attributes"] ?? null), "second", [], "any", false, false, true, 34), "addClass", ["layout__region", "layout__region--second"], "method", false, false, true, 34), 34, $this->source), "html", null, true);
                echo ">
          ";
                // line 35
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "second", [], "any", false, false, true, 35), 35, $this->source), "html", null, true);
                echo "
        </div>
      ";
            }
            // line 38
            echo "
      ";
            // line 39
            if (twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "third", [], "any", false, false, true, 39)) {
                // line 40
                echo "        <div";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["region_attributes"] ?? null), "third", [], "any", false, false, true, 40), "addClass", ["layout__region", "layout__region--third"], "method", false, false, true, 40), 40, $this->source), "html", null, true);
                echo ">
          ";
                // line 41
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "third", [], "any", false, false, true, 41), 41, $this->source), "html", null, true);
                echo "
        </div>
      ";
            }
            // line 44
            echo "
      ";
            // line 45
            if (twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "fourth", [], "any", false, false, true, 45)) {
                // line 46
                echo "        <div";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["region_attributes"] ?? null), "fourth", [], "any", false, false, true, 46), "addClass", ["layout__region", "layout__region--fourth"], "method", false, false, true, 46), 46, $this->source), "html", null, true);
                echo ">
          ";
                // line 47
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "fourth", [], "any", false, false, true, 47), 47, $this->source), "html", null, true);
                echo "
        </div>
      ";
            }
            // line 50
            echo "
      ";
            // line 51
            if (twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "fifth", [], "any", false, false, true, 51)) {
                // line 52
                echo "        <div";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["region_attributes"] ?? null), "fifth", [], "any", false, false, true, 52), "addClass", ["layout__region", "layout__region--fifth"], "method", false, false, true, 52), 52, $this->source), "html", null, true);
                echo ">
          ";
                // line 53
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "fifth", [], "any", false, false, true, 53), 53, $this->source), "html", null, true);
                echo "
        </div>
      ";
            }
            // line 56
            echo "
    ";
            // line 57
            if ((((twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "second", [], "any", false, false, true, 57) || twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "third", [], "any", false, false, true, 57)) || twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "fourth", [], "any", false, false, true, 57)) || twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "fifth", [], "any", false, false, true, 57))) {
                // line 58
                echo "      </div>
    ";
            }
            // line 60
            echo "
  </div>
";
        }
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["content", "attributes", "region_attributes"]);    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "core/profiles/demo_umami/themes/umami/layouts/oneplusfourgrid_section/layout--oneplusfourgrid-section.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  144 => 60,  140 => 58,  138 => 57,  135 => 56,  129 => 53,  124 => 52,  122 => 51,  119 => 50,  113 => 47,  108 => 46,  106 => 45,  103 => 44,  97 => 41,  92 => 40,  90 => 39,  87 => 38,  81 => 35,  76 => 34,  74 => 33,  71 => 32,  67 => 30,  65 => 29,  62 => 28,  56 => 25,  51 => 24,  49 => 23,  43 => 21,  41 => 20,  39 => 15,);
    }

    public function getSourceContext()
    {
        return new Source("", "core/profiles/demo_umami/themes/umami/layouts/oneplusfourgrid_section/layout--oneplusfourgrid-section.html.twig", "/Users/nguyenlam/Testing/my_site_name_dir/core/profiles/demo_umami/themes/umami/layouts/oneplusfourgrid_section/layout--oneplusfourgrid-section.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 15, "if" => 20);
        static $filters = array("escape" => 21);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
