function gridListPrivilege(url,type) {
  $('#grid').treegrid({
    url         : url,
    queryParams : {"user_group_id": $('#userGroup').val(), "type": type},
    method      : 'POST',
    striped     : true,
    fitColumns  : true,
    height      : 'auto',
    toolbar     : '#tb',
    treeField   : 'name',
    idField     : 'menu_id',
    emptyMsg    : 'No records found.',
    minHeight   : '160px',
    scrollbarSize: 0,
    nowrap      : true,
    columns:[[
      { field: 'name', title: 'Menu Name', width: 50, align: 'left'},
      { field: 'action', title: 'Action', width: 100, align: 'left', formatter: function(value, row, index) {
        a = row.action;
        check = ''
        for (i = 0; i < a.length; i++){
          if(a[i].status == 1){
            checked = 'checked';
          }else{
            checked = '';
          }

          check += '<b class="switchery-xs">';
          check += '<label>';
          check += '<input type="checkbox" class="switchery" '+checked+' onclick="savePrevilege(this,'+a[i].detail_id+')" group="'+a[i].user_group_id+'" menu_id="'+a[i].menu_id+'" menu_detail="'+a[i].detail_id+'" privilege="'+a[i].privilege_id+'">';
          check += ' '+a[i].action+'</label>';
          check += '</b>';
          check += '&nbsp;&nbsp;&nbsp;';
        }

        return check;
      }},
    ]],

    loadFilter:function(data){
      if(data.code == '0'){
        return data.data;
      }

      if(data.code == '108'){
        notification('error',data.message, function(){
          logout();
        });
      }
    },

    onLoadSuccess: function(row){
      $("#ctrl").click(function() {
        $('#grid').treegrid('resize');
      });

      $(function() {
          if (Array.prototype.forEach) {
            var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));
            elems.forEach(function(html) {
              var switchery = new Switchery(html);
            });
          }
      });
    }
  });
}