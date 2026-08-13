const PluginManager = window.PluginManager;

PluginManager.register(
    'AddToCompareButton',
    () => import('./plugin/add-to-compare-button.plugin'),
    '[data-add-to-compare-button]'
);
PluginManager.register(
    'CompareWidget',
    () => import('./plugin/compare-widget.plugin'),
    '[data-compare-widget]'
);
PluginManager.register(
    'CompareFloat',
    () => import('./plugin/compare-float.plugin'),
    '[data-compare-float]'
);
