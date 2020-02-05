window.onload = function(){
    var tagbtn = document.getElementById("fiy_tagbtn");
    var tags = document.getElementById("fiy_tags");
    var inputtag = document.getElementById("fiy_tag");

    inputtag.addEventListener("keyup",function() {
        var len = document.getElementById("fiy_tag").value.length;
        if (len >= 5) {
            tagbtn.disabled = false;
        } else {
            tagbtn.disabled = true;
        }
    });
    tagbtn.addEventListener("click",function() {
        var tag = document.getElementById("fiy_tag").value;
        if (tag) {
            $insert = true;
            var date = tags.querySelectorAll('.tag');
            array = [].slice.call(date);

            array.forEach( function ( array ) {
                if (array.children[0].defaultValue == tag){
                    $insert =  false;
                }
            });
            if ($insert) {
                var additem = document.createElement('span');
                additem.className = 'tag';
                additem.innerHTML = '<input type="hidden" name="fyi_notice_setting_option[tag][]" value="' + tag + '">' + tag;
                tags.appendChild(additem);
                document.getElementById("fiy_tag").value = '';
            }
        }
    });
    tags.addEventListener("click", (event) => {
        var result = confirm('delete it?');
        if(result) {
            tags.removeChild(event.target);
        }
    });
}

