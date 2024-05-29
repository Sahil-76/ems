
function itemDelete(link)
{
if(confirm('Are you sure ?'))
{
$.ajax({
    url:link,
    type:'get',
    success:function(response)
    {
        toastr.success(response);
        location.reload();
    },
    error:function(error)
    {
        alert('somethng went wrong');
    }


});
}
}