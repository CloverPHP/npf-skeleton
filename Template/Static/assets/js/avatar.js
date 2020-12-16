
// 用法
// createAvatar("#avatar", function (src) {
//     console.log(src);
// });


// 请求
// createAvatar("#avatar", function (src) {
//     $.ajax({
//         url: '/Profile/UpdateAvatar',
//         type: 'POST',
//         dataType: 'json',
//         data: src,
//         success: function (res) {
//             if (res.status === 'ok') {
//                 console.log(res);
//                 alert("success");
//                 location.reload();
//             } else {
//                 console.log(res);
//                 alert("fail");
//             }
//         },
//         error: function (error) {
//             console.log(error);
//             alert("error");
//         }
//     });
// });


function createAvatar(wrap, onchange) {
    var $wrap = $(wrap);
    console.log($wrap)
    var $ipt = $("<input type='file'/>");
    var $img = $("<img src='" + $wrap.attr("data-src") + "' alt=''>")
    $wrap.empty();
    $wrap.css({
        position: "relative",
    })
    $ipt.css({
        zIndex: 2,
        opacity: 0,
        position: "absolute",
        left: 0,
        top: 0,
        width: "100%",
        height: "100%",
    })
    $img.css({
        zIndex: 1,
        position: "absolute",
        top: 0,
        left: 0,
        width: "100%",
        height: "100%",
    })
    $wrap.append($ipt);
    $wrap.append($img);

    $ipt.on("change",function(e){
        var files = e.currentTarget.files;
        var reader = new FileReader();
        reader.readAsDataURL(files[0]);
        reader.onload = function(){
            var src = reader.result;
            $wrap.attr("data-src",src)
            $img.attr("src",src);
            onchange(files[0])
        }
    })
}
