import io from './../../node_modules/socket.io-client/';

const socket = io(serverUrl);
socket.on('connect', onConnect);

function onConnect() {
  console.log('connect ' + socket.id);
}

require('./main');
require('./submit');

class Search {

  // TODO: limit to maximum 3 tags
  // TODO: les tags déjà 'actifs' doivent être grisé (en dessous de l'input)

  constructor(socket) {
    this.socket = socket;

    this.tagsWidth = 0;
    this.primaryInput = document.getElementById('home-search-box-input');
    this.primaryTags = document.getElementById('primary-search-box-tags');
    this.staticSearchTags = Array.prototype.slice.call(document.querySelectorAll('.static-tag-label'), 0);
    this.dynamicTags = document.getElementById("primary-search-box-tags").getElementsByClassName("tag");
    this.searchResultBox = document.getElementById('primary-search-box').getElementsByClassName("results-content");
    this.activeSearchTags = [];
    this.searchParameters = {};

    this.uri = new Uri();
    this.tools = new Tools();

    // from request
    this.setSearchParametersFromUri();
    this.updatePrimaryInputFromSearchParameters();

    // init
    this.watchStaticTags();
    this.watchDynamicTags();
    this.watchPrimaryInput();
    this.refreshPrimaryInput();

    this.doSearch();
  }

  calculateTagWidth() {
    this.tagsWidth = this.primaryTags.offsetWidth;
  }

  generateHtmlDynamicTag(tag) {

    let span = document.createElement("span");
    let label = document.createTextNode(tag.label);
    let button = document.createElement("button");
    span.className = 'tag is-white';
    span.setAttribute('data-label', tag.label);
    span.setAttribute('data-facet', tag.facet);
    span.appendChild(label);
    button.className = 'delete is-small dynamic-tag-label-delete';
    span.appendChild(button);

    return span;
  }

  addTag(tag) {
    let index = this.activeSearchTags.indexOf(tag.label);
    if (index === -1) {
      this.activeSearchTags.push(tag.label);
      this.primaryTags.appendChild(this.generateHtmlDynamicTag(tag));
    }

    this.refreshPrimaryInput();
  }

  removeTag(tag) {
    let tag_label = tag.getAttribute('data-label');

    let index = this.activeSearchTags.indexOf(tag_label);
    if (index > -1) {
      this.activeSearchTags.splice(index, 1);
      tag.parentNode.removeChild(tag);
    }

    this.refreshPrimaryInput();
  }

  watchStaticTags() {

    if (this.staticSearchTags.length > 0) {
      this.staticSearchTags.forEach($el => {
        $el.addEventListener('click', (event) => {
          event.preventDefault();
          let tag = {};
          tag.label = $el.getAttribute('data-label');
          tag.facet = $el.getAttribute('data-facet');
          this.addTag(tag);

          this.doSearch();
        });
      });
    }
  }

  watchDynamicTags() {
    const fc = this;
    document.onclick = event => {
      let $el = event.target;

      // delete dynamic search tag
      if ($el.classList.contains("dynamic-tag-label-delete") && $el.nodeName === "BUTTON") {
        event.preventDefault();
        fc.removeTag($el.parentElement);

        this.doSearch();
      }
    };
  }

  watchPrimaryInput() {
    // typing
    this.primaryInput.onkeyup = () => {
      let self = event.target;
      this.doSearch();

      // hide result box if empty search
      if (self.value.length === 0) {
        this.searchResultBox[0].style.display = 'none';
      }
    };

    // detect backspace
    this.primaryInput.onkeydown = (event) => {
      let key = event.keyCode || event.charCode;
      let self = event.target;

      if (self.value.length === 0 && (key === 8 || key === 46)) {
        this.removeLastDynamicTag();
      }
    };
  }

  removeLastDynamicTag() {
    let lastDynamicTag = this.dynamicTags[this.dynamicTags.length - 1];

    if (lastDynamicTag) {
      this.removeTag(lastDynamicTag);
    }
  }

  refreshPrimaryInput() {
    this.calculateTagWidth();
    this.primaryInput.style.textIndent = `${this.tagsWidth}px`;
    this.primaryInput.focus();
  }

  refreshSearchParameters() {
    this.searchParameters.q = this.primaryInput.value;
    this.searchParameters.tags = Array.prototype.slice.call(this.dynamicTags).map(function ($el) {
      let tag = {};
      tag.label = $el.getAttribute('data-label');
      tag.facet = $el.getAttribute('data-facet');

      return tag;
    });
  }

  doSearch() {
    // parameters
    this.refreshSearchParameters();
    this.updateUri();

    this.socket.emit('search', this.searchParameters);

  }

  updateUri() {
    let location = window.location.href;

    location = this.uri.updateQueryStringParameter(location, 'q', this.searchParameters.q);
    location = this.uri.updateQueryStringParameter(location, 'tags', JSON.stringify(this.searchParameters.tags));

    window.history.replaceState('', '', location);

    return location;
  }

  setSearchParametersFromUri() {
    let location_query_params = this.uri.getQueryParams(document.location.search);

    if (typeof location_query_params.q !== 'undefined' && location_query_params.q) {
      this.searchParameters.q = location_query_params.q;
    }

    if (typeof location_query_params.tags !== 'undefined' && location_query_params.tags) {
      this.searchParameters.tags = JSON.parse(location_query_params.tags);
    }
  }

  updatePrimaryInputFromSearchParameters() {

    if (typeof this.searchParameters.tags !== 'undefined' && this.searchParameters.tags) {
      this.searchParameters.tags.forEach($el => {

        let tag = {};
        tag.label = $el.label;
        tag.facet = $el.facet;
        this.addTag(tag);
      });
    }

    if (typeof this.searchParameters.q !== 'undefined') {
      this.primaryInput.value = this.searchParameters.q;
    }
  }

  clearSearch() {
    this.searchParameters.q = null;
    this.searchParameters.tags = null;

    Array.from(this.dynamicTags).forEach((item) => {
      this.removeTag(item);
    });
  }

  purposeRandomExample() {
    let searchExamples = {
      'nodejs-expressjs-routes':    {
        'q':    'Routes',
        'tags': [
          {"label": "nodejs", "facet": "languages"}, {"label": "expressjs", "facet": "tags"}
        ]
      },
      'php-symfony-doctrine-cache': {
        'q':    'Cache',
        'tags': [
          {"label": "php", "facet": "languages"}, {"label": "symfony", "facet": "tags"}, {
            "label": "doctrine",
            "facet": "tags"
          }
        ]
      },
      'expressjs-bootstrap':        {
        'q':    '',
        'tags': [
          {"label": "expressjs", "facet": "tags"}, {"label": "bootstrap", "facet": "tags"}
        ]
      }
    };
    let searchExample = this.tools.pickRandomProperty(searchExamples);

    this.clearSearch();

    this.searchParameters.q = searchExample.q;
    this.searchParameters.tags = searchExample.tags;

    this.updatePrimaryInputFromSearchParameters();
    this.doSearch();
  }

  listenForResult() {
    let self = this;
    this.socket.on('found', function (data) {
      self.showResult(data);
    });
  }

  showResult(data) {
    let hits = data.hits;
    let searchResultBox = this.searchResultBox[0];
    searchResultBox.innerHTML = ''; // empty box

    if (hits.length === 0) {
      searchResultBox.style.display = 'none';
      return false;
    }

    searchResultBox.style.display = 'block';

    let current_column;
    // cards
    hits.forEach(($el, index) => {
      // every 3 cols, add columns element
      if (index % 3 === 0 || index === 0) {
        current_column = document.createElement("div");
        current_column.className = 'columns';
      }

      current_column.appendChild(this.generateResultCard($el));

      // every 3 cols, append to columns element
      if (index % 3 === 0) {
        searchResultBox.appendChild(current_column);
      }

    });


  }

  generateResultCard(hit) {
    let column = document.createElement("div");
    let card = document.createElement("div");
    let card_content = document.createElement("div");
    let title = document.createElement('a');
    column.className = 'column is-one-third';
    column.setAttribute('data-id', hit._id);
    column.setAttribute('data-slug', hit.slug);

    card.className = 'card';
    card_content.className = 'card-content';

    title.setAttribute('href', hit.internal_url);
    title.setAttribute('target', '_blank');
    title.innerHTML = hit.name;

    card_content.appendChild(title);
    card.appendChild(title);
    column.appendChild(card);

    return column;
  }
}

class Uri {

  getQueryParams(qs) {
    qs = qs.split('+').join(' ');

    let params = {};
    let tokens;
    let re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
      params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }

    return params;
  }

  updateQueryStringParameter(uri, key, value) {
    const re = new RegExp(`([?&])${key}=.*?(&|$)`, "i");
    const separator = uri.includes('?') ? "&" : "?";
    if (uri.match(re)) {
      return uri.replace(re, `$1${key}=${value}$2`);
    }
    else {
      return `${uri + separator + key}=${value}`;
    }
  }
}

class Tools {
  pickRandomProperty(obj) {
    let keys = Object.keys(obj);
    return obj[keys[keys.length * Math.random() << 0]];
  }
}

/**
 *
 */
window.addEventListener("load", () => {

  // Search (need page fully loaded => css)
  if (document.body.classList.contains("homepage") && document.getElementById('primary-search-box')) {
    const search = new Search(socket);

    document.getElementById('search-random-example').addEventListener('click', (event) => {
      event.preventDefault();
      search.purposeRandomExample();
    });

    search.listenForResult();

  }
});

/**
 *
 */
document.addEventListener("DOMContentLoaded", () => {

});
