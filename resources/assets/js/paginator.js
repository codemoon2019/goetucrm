/**
 * Created by Jianfeng Li on 2017/5/12.
 */

class Paginator {
    constructor(pageObject, path = "/", pageName = "page") {
        this.pageObject = pageObject;
        this.path = path;
        this.pageName = pageName;
    }

    currentPage() {
        return parseInt(this.pageObject["current_page"]);
    }

    previousPage() {
        if (this.currentPage() > 1) {
            return this.currentPage() - 1;
        } else {
            return 1;
        }
    }

    nextPage() {
        if (this.lastPage() > this.currentPage()) {
            return this.currentPage() + 1;
        } else {
            return this.currentPage();
        }
    }

    onFirstPage() {
        return this.currentPage() <= 1;
    }

    lastPage() {
        return parseInt(this.pageObject["last_page"]);
    }

    hasMorePages() {
        return this.currentPage() < this.lastPage();
    }

    total() {
        return parseInt(this.pageObject["total"]);
    }

    perPage() {
        return parseInt(this.pageObject["per_page"]);
    }

    items() {
        return this.pageObject.data;
    }

    previousPageUrl() {
        if (this.currentPage() > 1) {
            return this.url(this.currentPage() - 1);
        }
    }

    nextPageUrl() {
        if (this.lastPage() > this.currentPage()) {
            return this.url(this.currentPage() + 1);
        }
    }

    url(page) {
        if (page < 0) {
            page = 1;
        }
        if ("/" === this.path) {
            return this.pageObject["prev_page_url"];
        } else {
            return this.path + (this.path.indexOf("?") > 0 ? "&" : "?") + this.pageName + "=" + page;
        }
    }
}

export default Paginator;