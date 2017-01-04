function updateTime(name) {
var date = new Date();
 var time = date.getHours() + ":" + date.getMinutes();
   $("[name='" + name + "']").val(time);
}