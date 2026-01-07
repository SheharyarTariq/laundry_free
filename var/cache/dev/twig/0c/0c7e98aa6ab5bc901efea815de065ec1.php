<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* emails/password_reset.html.twig */
class __TwigTemplate_cf9be1edafd7338781a443eba64d6368 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "emails/password_reset.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "emails/password_reset.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #FF6B6B;
            margin: 0;
        }
        .code-box {
            background-color: #fff;
            border: 2px solid #FF6B6B;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .reset-code {
            font-size: 36px;
            font-weight: bold;
            color: #FF6B6B;
            letter-spacing: 8px;
            margin: 10px 0;
        }
        .message {
            font-size: 16px;
            color: #555;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .warning {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 20px;
        }
        .security-notice {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class=\"container\">
        <div class=\"header\">
            <h1>🧺 Laundry Free</h1>
            <p>Password Reset Request</p>
        </div>
        
        <div class=\"message\">
            <p>We received a request to reset your password.</p>
            <p>Use the following code to reset your password:</p>
        </div>
        
        <div class=\"code-box\">
            <p style=\"margin: 0; font-size: 14px; color: #777;\">Your Reset Code</p>
            <div class=\"reset-code\">";
        // line 85
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["resetCode"]) || array_key_exists("resetCode", $context) ? $context["resetCode"] : (function () { throw new RuntimeError('Variable "resetCode" does not exist.', 85, $this->source); })()), "html", null, true);
        yield "</div>
        </div>
        
        <div class=\"message\">
            <p>Enter this code along with your new password to complete the reset.</p>
            <p class=\"warning\">⚠️ This code will expire in 15 minutes.</p>
        </div>
        
        <div class=\"security-notice\">
            <p style=\"margin: 0;\"><strong>🔒 Security Notice:</strong></p>
            <p style=\"margin: 5px 0 0 0;\">If you didn't request this password reset, please ignore this email and your password will remain unchanged.</p>
        </div>
        
        <div class=\"footer\">
            <p>This is an automated message, please do not reply.</p>
            <p>&copy; 2024 Laundry Free. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "emails/password_reset.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  134 => 85,  48 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #FF6B6B;
            margin: 0;
        }
        .code-box {
            background-color: #fff;
            border: 2px solid #FF6B6B;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .reset-code {
            font-size: 36px;
            font-weight: bold;
            color: #FF6B6B;
            letter-spacing: 8px;
            margin: 10px 0;
        }
        .message {
            font-size: 16px;
            color: #555;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .warning {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 20px;
        }
        .security-notice {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class=\"container\">
        <div class=\"header\">
            <h1>🧺 Laundry Free</h1>
            <p>Password Reset Request</p>
        </div>
        
        <div class=\"message\">
            <p>We received a request to reset your password.</p>
            <p>Use the following code to reset your password:</p>
        </div>
        
        <div class=\"code-box\">
            <p style=\"margin: 0; font-size: 14px; color: #777;\">Your Reset Code</p>
            <div class=\"reset-code\">{{ resetCode }}</div>
        </div>
        
        <div class=\"message\">
            <p>Enter this code along with your new password to complete the reset.</p>
            <p class=\"warning\">⚠️ This code will expire in 15 minutes.</p>
        </div>
        
        <div class=\"security-notice\">
            <p style=\"margin: 0;\"><strong>🔒 Security Notice:</strong></p>
            <p style=\"margin: 5px 0 0 0;\">If you didn't request this password reset, please ignore this email and your password will remain unchanged.</p>
        </div>
        
        <div class=\"footer\">
            <p>This is an automated message, please do not reply.</p>
            <p>&copy; 2024 Laundry Free. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
", "emails/password_reset.html.twig", "/Users/mac-2019/Documents/projects/laundry-free/templates/emails/password_reset.html.twig");
    }
}
