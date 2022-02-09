/**************************************************************************/
/**************                   THUMBNAIL                  **************/
/**************************************************************************/

const thumbnails = document.querySelectorAll('.focus-thumbnail-img');
if (thumbnails) {
    for ( var i=0; i<thumbnails.length; i++) {
        thumbnails[i].addEventListener('click',function() {
            document.getElementById('focus-product-zoom-img').src = this.src;

            if (this.className === 'focus-thumbnail-img') {
                let imageActive = document.querySelector('.active');
                imageActive.classList.remove('active');
                this.className = 'focus-thumbnail-img active';
            }
        })
    }
}

/**************************************************************************/
/**************               PRODUCT DETAILS                **************/
/**************************************************************************/

var desc = document.querySelector('#focus-product-description');

if (desc) {
    desc.addEventListener('click',()=>{
        document.getElementById('show-default').style.display='inline';
        document.getElementById('hidden').style.display='none';
    })
}

var compo = document.querySelector('#focus-product-composition');
if (compo) {
    compo.addEventListener('click',()=>{
        document.getElementById('hidden').style.display='inline';
        document.getElementById('show-default').style.display='none';
    })
}