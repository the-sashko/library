let App = {
    init: function() {
        App.initFileManagerView();
    },

    initFileManagerView: function() {
        document.getElementById('greed-view-button').onclick = function() {
            App.greedView();
        };

        document.getElementById('list-view-button').onclick = function() {
            App.listView();
        };
    },

    greedView: function() {
        let fileManager = document.getElementById('file-manager');

        let greedViewButtonWrapper = document.getElementById(
            'greed-view-button-wrapper'
        );

        let listViewButtonWrapper = document.getElementById(
            'list-view-button-wrapper'
        );

        fileManager.classList.remove('file-manager-list');
        fileManager.classList.add('file-manager-greed');

        greedViewButtonWrapper.classList.add('hidden');
        listViewButtonWrapper.classList.remove('hidden');
    },

    listView: function() {
        let fileManager = document.getElementById('file-manager');

        let greedViewButtonWrapper = document.getElementById(
            'greed-view-button-wrapper'
        );

        let listViewButtonWrapper = document.getElementById(
            'list-view-button-wrapper'
        );

        fileManager.classList.remove('file-manager-greed');
        fileManager.classList.add('file-manager-list');

        listViewButtonWrapper.classList.add('hidden');
        greedViewButtonWrapper.classList.remove('hidden');
    }
};

App.init();
