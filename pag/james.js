const galleryContainer = document.querySelector('.gallery-container');
const galleryControlsContainer = document.querySelector('.gallery-controls');
const galleryControls = ['previous', 'next'];
const galleryItems = document.querySelectorAll('.gallery-item');

class Carousel {

    constructor(container, items , controls){
    this.carouselContainer = container;
    this.carouselControls = controls;
    this.CarouselArray =  [...items];
    }

    updateGallery(){
        this.CarouselArray.forEach(el => {
            el.classList.remove('gallery-item-1');
            el.classList.remove('gallery-item-2');
            el.classList.remove('gallery-item-3');
            el.classList.remove('gallery-item-4');
            el.classList.remove('gallery-item-5');
        });
        this.CarouselArray.slice(0, 5).forEach((el , i) => {
            el.classList.add('gallery-item-${i+1}');
        });
    }
    setCurrentState(direction){
        if (direction.className == 'gallery-controls-previous') {
            this.CarouselArray.unshift(this.CarouselArray.pop());
        }else{
            this.CarouselArray.push(this.CarouselArray.shift());
        }
        this.updateGallery();
    }
    setControls(){
        this.carouselControls.forEach(control => {
            galleryControlsContainer.appendChild(document.createElement('button')).className = 'gallery-controls-${control}';
            document.querySelector('.gallery-controls-${control}').innerText = control;
        });
    }

    useControls(){
        const triggers = [...galleryControlsContainer.childNodes];
        triggers.forEach(control => {
            control.addEventListener('click', e => {
                e.previousDefault();
                this.setCurrentState(control);
            });
        })
    }
}

const exampleCarousel = new Carousel(galleryContainer, galleryItem , galleryControls);

exampleCarousel.setControls();
exampleCarousel.useControls();