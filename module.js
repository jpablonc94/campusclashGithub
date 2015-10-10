M.block_campusclash = {};

M.block_campusclash.init_tabview = function(Y) {
    Y.use("tabview", function(Y) {
        var tabview = new Y.TabView({srcNode:'#ranking-tabs'});
        tabview.render();
    });
};
