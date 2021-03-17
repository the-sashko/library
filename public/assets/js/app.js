let App = {
    init: function() {
        App.initFileManagerView();
    },

    init: function() {
        App.initForm();
    },

    initFileManagerView: function() {
        if (document.getElementById('greed-view-button') != null) {
            App.initFileManagerGreedView();
        }

        if (document.getElementById('list-view-button') != null) {
            App.initFileManagerListView();
        }
    },

    initForm: function() {
        if (document.getElementById('file-form') != null) {
            App.initFileForm();
        }
    },

    initFileForm: function() {
        document.getElementById('file-form-input-file').onchange = function() {
            let inputValue = document.getElementById('file-form-input-file').value;

            inputValue = inputValue.split('/');
            inputValue = inputValue[inputValue.length - 1];

            inputValue = inputValue.split('\\');
            inputValue = inputValue[inputValue.length - 1];

            if (inputValue.length > 26) {
                inputValue = inputValue.substring(0, 13) +
                             '…' +
                             inputValue.substring(inputValue.length - 12);
            }

            inputValue = inputValue.toLowerCase();

            document.getElementById('form-input-file-name').innerHTML = '<i class="fa fa-link"></i> ' +
                                                                        inputValue;

            document.getElementById('form-input-file-name').style.display = 'block';
        };
    },

    initFileManagerGreedView: function() {
        document.getElementById('greed-view-button').onclick = function() {
            App.greedView();
        };
    },

    initFileManagerListView: function() {
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
