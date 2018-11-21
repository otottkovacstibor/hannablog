import initDatatable from './manage/datatable';
import initDatatableInteractions from './manage/interactions';
import '../../css/admin/manage.scss';

jQuery(document).ready(function($) {
	if(jQuery('.wprm-manage-datatable').length > 0) {
		initDatatable();
		initDatatableInteractions();
	}
});