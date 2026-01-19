import CompareWidgetPlugin from './plugin/compare-widget.plugin';
import AddToCompareButtonPlugin from './plugin/add-to-compare-button.plugin';
import CompareFloatPlugin from './plugin/compare-float.plugin';

window.PluginManager.register(
    'AddToCompareButton',
    AddToCompareButtonPlugin,
    '[data-add-to-compare-button]'
);
window.PluginManager.register(
    'CompareWidget',
    CompareWidgetPlugin,
    '[data-compare-widget]'
);
window.PluginManager.register(
    'CompareFloat',
    CompareFloatPlugin,
    '[data-compare-float]'
);
