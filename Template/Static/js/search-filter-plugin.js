class SearchFilterPlugIn {

    constructor() {
        this.isConfirm = false;
        this.searchForm = $("form#search-filter-plugin-form");
        this.currentPage = parseInt(this.searchForm.find('input[type="hidden"][name="_pagination.page_"]').val(), 10);
        this.totalPage = parseInt(this.searchForm.find('input[type="hidden"][name="_pagination.total_"]').val(), 10);
        this.pageSize = parseInt(this.searchForm.find('input[type="hidden"][name="_pagination.size_"]').val(), 10);
        this.pagesElement = 'li.page-item';
        this.pageSizeElement = 'select.page-item';
        this.search = {};
        this.event = {};
        this.defaultSearch = {};
        if (isNaN(this.pageSize))
            this.pageSize = 20;
        if (isNaN(this.currentPage))
            this.currentPage = 0;
        if (isNaN(this.totalPage))
            this.totalPage = 0;
    }

    setPagesElement(pagesElement) {
        if (pagesElement !== '' && $(pagesElement).length > 0)
            this.pagesElement = pagesElement;
    }

    setForm(form) {
        if ($(form).tagName === 'FORM') {
            this.searchForm = $(form);
            return true;
        } else
            return false;
    };

    submit() {
        if (this.isConfirm)
            return false;
        this.isConfirm = true;
        var formItem = this.searchForm.serializeArray();
        for (var i = 0; i < formItem.length; i++)
            if (formItem[i].name !== '')
                this.addSearchItem(formItem[i].name, formItem[i].value);
        this.removeSearchItem('_pagination.page_');
        this.removeSearchItem('_pagination.total_');
        this.addSearchItem('action', 'search');
        common.showWait();
        common.formPost(Object.assign(this.search, {page: this.currentPage, pageSize: this.pageSize}));
        return false;
    };

    setDefaultSearch(params) {
        this.defaultSearch = params;
        Object.assign(this.search, this.defaultSearch);
    };

    getSearchItem(name) {
        if (name === '*')
            return this.search;
        else
            return this.search[name];
    };

    addSearchItem(name, value) {
        this.search[name] = value;
    };

    removeSearchItem(name) {
        if (this.search.hasOwnProperty(name))
            delete this.search[name];
    };

    resetSearch() {
        if (this.isConfirm)
            return false;
        this.isConfirm = true;
        common.formPost(Object.assign(this.defaultSearch, {action: 'resetSearch', pageSize: this.pageSize}));
    };

    onSearch(callBack) {
        let name = 'search';
        if (!this.event.hasOwnProperty(name) || !Array.isArray(this.event[name]))
            this.event[name] = [];
        this.event[name].push(callBack);
    };

    onInit(callBack) {
        let name = 'init';
        if (!this.event.hasOwnProperty(name) || !Array.isArray(this.event[name]))
            this.event[name] = [];
        this.event[name].push(callBack);
    };

    emit(name) {
        let me = this;
        if (me.event.hasOwnProperty(name) && Array.isArray(me.event[name])) {
            me.event[name].forEach(function (callBack) {
                if (typeof (callBack) === 'function')
                    callBack.apply(me);
            });
        }
    };

    init() {
        var me = this;
        if (me.searchForm.children().length > 0) {
            me.searchForm.submit(function (e) {
                e.preventDefault();
                me.currentPage = 0;
                me.submit();
            });
            me.searchForm.find('input[type="reset"], button[type="reset"]').click(function (e) {
                me.resetSearch(e);
            });
        }
        $(this.pagesElement).on("click", function (e) {
            e.preventDefault();
            if ($(this).hasClass("active"))
                return;
            var page = $(this).data("page");
            switch (page) {
                case "prev":
                    me.currentPage -= 1;
                    break;
                case "next":
                    me.currentPage += 1;
                    break;
                case "first":
                    me.currentPage = 1;
                    break;
                case "last":
                    me.currentPage = me.totalPage;
                    break;
                default:
                    me.currentPage = page;
            }
            me.submit();
        });
        $(this.pageSizeElement).on("change", function (e) {
            e.preventDefault();
            var pageSize = $(this).val();
            if (!isNaN(pageSize)) {
                me.pageSize = pageSize;
                me.currentPage = 0;
                me.submit();
            }
        });
        me.emit('init');
    };
}

let searchFilterPlugIn = new SearchFilterPlugIn();

$(document).ready(() => {
    searchFilterPlugIn.init();
});