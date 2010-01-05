var Wistlet = {
        init : function () {
                var o = document.getElementsByTagName('body').item(0);

                //var text = (typeof(document.selection == 'undefined') ) ? getSelection() : document.selection.createRange().text;

                function getSel() {
                        var txt = '';
                        if (window.getSelection) {
                                txt = window.getSelection();
                        } else if (document.getSelection) {
                                txt = document.getSelection();
                        } else if (document.selection) {
                                txt = document.selection.createRange().text;
                        } else {
                                return;
                        }
                        return txt;
                }


                var s = '' +
                '<form name="f" id="f" action="http://www.wists.com/s.php" method="get">'+
                '<input name="c" id="c" type="hidden" value="null" />'+
                '<input name="r" id="r" type="hidden" value="'+location+'" />'+
                '<input name="u" id="u" type="hidden" value="" />'+
                '<input name="title" id="title" type="hidden" value="'+document.title +'" />'+
                '<input name="m" type="hidden" value="'+getSel()+'" />'+
                '</form>'+
                '<div>'+
                '<a href="http://wists.com/"><img src="http://wists.com/mainimages/logo_top_left.gif" border=0></a><br />'+
                '<p>Click on the most appropriate image below to create a thumbnail image for your bookmark:</a></p>'+
                '<table width=800><tr width=800><td width=800>\n';

                var x = this.scrape_images();
                if (x == 0) 
                {
                        location = 'http://www.wists.com/s.php?c=&r='+location+'&title='+document.title;
                        return true;
                }
                s += x +

                '</td></tr></table>\n'+
                '<p>Don\'t like any of these images? <a href="http://www.wists.com/s.php?c=&r='+location+'&title='+document.title +'">Create a thumbnail screenshot</a> instead!</p>'+
                '</div>';

                o.innerHTML = s;
                this.strip_document();
                this.style_document();
                return true;
        },
        make_thumbnail : function (r) {
                var s = '' +
                '<table style="float:left;"><tr><td width=120 height=90 valign=middle align=center><a href="http://www.wists.com/" onclick="return Wistlet.submit_form(\''+r+'\');">'+
                '<img src="'+r+'" alt="" onload="resizeImage(this);" />'+
                '</a></td></tr></table>\n';
                return s;
        },
        submit_form : function (r) {
                var f = document.getElementById('f'); if (!f) return false;
                var u = document.getElementById('u'); if (!u) return false;
                u.setAttribute("value", r);
                f.submit();
                return false;
        },
        get_background : function (o) {
                var s = '';
                if (window.getComputedStyle) s = window.getComputedStyle(o,null).getPropertyValue("background-image");
                if (o.currentStyle) s = o.currentStyle.backgroundImage;
                if (s == "none") s = "";
                return s;
        },
        scrape_images : function () {
                var a = document.getElementsByTagName('*');
                var s = '';
                var n = 0;
                var l = [];
                for (var i=0; i < a.length; i++) 
                {
                        var o = a[i];
                        if (o.tagName == "IMG" && o.src != "")
                        {
                                n++;
                                if (!l[o.src])
                                {
                                        l[o.src] = "1";
                                        s += this.make_thumbnail(o.src);
                                }
                        }
                        else 
                        {
                                var x = /url\(["']?(.+[^'"])["']?\)/gi.exec(this.get_background(o));
                                if (x && x != "" && x.length > 1) 
                                {
                                        n++;
                                        if (!l[x[1]])
                                        {
                                                l[x[1]] = "1";
                                                s += this.make_thumbnail(x[1]);
                                        }
                                }
                        }
                }
                if (n > 0) 
                {
                        return s 
                } 
                else {
                        return n;
                }
        },
        strip_document : function () {
                var a = document.getElementsByTagName('*');
                for (var i=0; i < a.length; i++) 
                {
                        var o = a[i];
                        if (o.tagName == "LINK" || o.tagName == "STYLE")
                        {
                                this.destroy_element(o);
                        }
                        else if (o.tagName == "IMG")
                        {
                                if (o.offsetWidth <= 10 || o.offsetHeight <= 10)
                                {
                        //              this.destroy_element(o.parentNode);
                                }
                        }
                }
                return true;
        },
        style_document : function () {
                var x = document.getElementsByTagName('head').item(0);
                var o = document.createElement('link');
                if (typeof o != 'object') o = document.standardCreateElement('link');
                o.setAttribute('href','http://www.wists.com/wistlet.css?x=' + Math.floor(Math.random() * 9999));
                o.setAttribute('rel','stylesheet');
                o.setAttribute('type','text/css');
                x.appendChild(o);
                return true;
        },
        destroy_element : function (o) {
                while (o.childNodes.length > 0) 
                {
                        o.removeChild(o.childNodes[0]);
                }
                o.parentNode.removeChild(o);
        }
}

function resizeImage(img) {

        var img_width = img.offsetWidth;
        var img_height = img.offsetHeight;
        var img_aspect_ratio = Math.round((img_width / img_height) * 100) / 100;

        var max_width = 120;
        var max_height = 90;
        var max_aspect_ratio = Math.round((max_width / max_height) * 100) / 100;

//      alert("orig image size is " + img_width + "x" + img_height + "\n" + "aspect ratio is " + img_aspect_ratio + "\n\n" + "max image size is " + max_width + "x" + max_height + "\n" + "max aspect ratio is " + max_aspect_ratio);

        var new_img_width = 0;
        var new_img_height = 0;
        var new_aspect_ratio = 0;

        // if no resize needed
        if (img_width < 120 && img_height < 90) {
                new_img_width = img_width;
                new_img_height = img_height; 

        // if wider
        } else if (img_aspect_ratio > max_aspect_ratio) {
                new_img_width = max_width;
                new_img_height = Math.round(new_img_width / img_aspect_ratio);

        // if taller
        } else if (img_aspect_ratio < max_aspect_ratio) {
                new_img_height = max_height;
                new_img_width = Math.round(new_img_height * img_aspect_ratio);

        // equal
        } else {
                new_img_width = max_width;
                new_img_height = max_height;
        }

        img.style.width = new_img_width + "px";
        img.style.height = new_img_height + "px";
        new_aspect_ratio = Math.round((new_img_width / new_img_height) * 100) / 100;

//      alert("new image size is " + new_img_width + "x" + new_img_height + "\n" + "new aspect ratio is " + new_aspect_ratio);
}

 
var sociable_old_onload = window.onload;
window.onload = function () {
    imgs = document.getElementsByTagName("img");
    for(var i = 0; i < imgs.length; i++) {
        var img = imgs[i];
        if (img.className == 'sociable-hovers sociable_wists') {
            img.onclick = function() { Wistlet.init(); return false; }
        }
    }
    if (sociable_old_onload) sociable_old_onload();
};
