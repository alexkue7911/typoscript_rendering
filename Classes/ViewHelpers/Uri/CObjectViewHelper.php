<?php
declare(strict_types=1);
namespace Helhum\TyposcriptRendering\ViewHelpers\Uri;

/*
 * This file is part of the TypoScript Rendering TYPO3 extension.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read
 * LICENSE file that was distributed with this source code.
 *
 */

use Helhum\TyposcriptRendering\Uri\TyposcriptRenderingUri;
use Helhum\TyposcriptRendering\Uri\ViewHelperContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * A view helper for creating URIs to render arbitrary TypoScript objects.
 *
 * = Examples =
 *
 * <code title="URI to the given rendering path">
 * <t:uri.cObject renderingPath="lib.userProfile"/>
 * </code>
 * <output>
 * index.php?id=123&tx_typoscriptrendering[context]={"record":"tt_content_123","path":"lib.userProfile"}&cHash=xyz
 * (depending on the current page and your TS configuration)
 * </output>
 */
class CObjectViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     *
     * @api
     */
    public function initializeArguments()
    {
        $this->registerArgument('typoscriptObjectPath', 'string', 'TypoScript rendering path');
        $this->registerArgument('contextRecord', 'string', 'The record that the rendering should depend upon. e.g. current (default: record is fetched from current Extbase plugin), tt_content:12 (tt_content record with uid 12), pages:15 (pages record with uid 15), \'currentPage\' record of current page', false, 'current');
        $this->registerArgument('pageUid', 'int', 'Target page. See TypoLink destination');
        $this->registerArgument('pageType', 'int', 'Type of the target page. See typolink.parameter', false, 0);
        $this->registerArgument('noCache', 'bool', 'Set this to disable caching for the target page. You should not need this.', false, false);
        $this->registerArgument('section', 'string', 'The anchor to be added to the URI', false, '');
        $this->registerArgument('format', 'string', 'The requested format, e.g. ".html', false, '');
        $this->registerArgument('linkAccessRestrictedPages', 'bool', 'If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.', false, false);
        $this->registerArgument('additionalParams', 'array', 'additional query parameters that won\'t be prefixed like $arguments (overrule $arguments)', false, []);
        $this->registerArgument('absolute', 'bool', 'If set, an absolute URI is rendered', false, false);
        $this->registerArgument('addQueryString', 'bool', 'If set, the current query parameters will be kept in the URI', false, false);
        $this->registerArgument('argumentsToBeExcludedFromQueryString', 'array', 'arguments to be removed from the URI. Only active if $addQueryString = TRUE', false, []);
        $this->registerArgument('addQueryStringMethod', 'string', 'Set which parameters will be kept. Only active if $addQueryString = TRUE');
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        // We need to set a dummy name here, otherwise Extbase will bail out when trying to determine the
        // plugin name from the request, which isn't set when in Extbase context
        // For this view helper, we don't need this anyway, so we set this to a dummy value
        $arguments['extensionName'] = 'DummyName';
        $uri = (new TyposcriptRenderingUri())->withViewHelperContext(
            new ViewHelperContext(
                $renderingContext,
                $arguments
            )
        );

        return (string)$uri;
    }
}
