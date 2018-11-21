if (!global._babelPolyfill) { require('babel-polyfill'); }
import './admin/editor';
import './admin/feedback';
import './admin/import';
import './admin/manage';
import './admin/modal';
import './admin/settings';
import './admin/tools';

import '../css/admin/addons.scss';
import '../css/admin/comments.scss';
import '../css/admin/faq.scss';

import './public/comment-rating';

import Modal from './admin/modal/Modal';
import Recipe from './admin/modal/Recipe';
import * as utils from './admin/modal/utils';
export { Modal, Recipe, utils };