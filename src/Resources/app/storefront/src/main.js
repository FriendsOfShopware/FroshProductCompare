import CompareWidgetPlugin from './plugin/compare-widget.plugin';
import AddToCompareButtonPlugin from './plugin/add-to-compare-button.plugin';
import CompareFloatPlugin from './plugin/compare-float.plugin';

const PluginManager = window.PluginManager;

PluginManager.register('AddToCompareButton', AddToCompareButtonPlugin, '[data-add-to-compare-button]');
PluginManager.register('CompareWidget', CompareWidgetPlugin, '[data-compare-widget]');
PluginManager.register('CompareFloat', CompareFloatPlugin, '[data-compare-float]');
