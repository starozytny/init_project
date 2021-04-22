import React, { Component } from "react";

export class GalleryThumbs extends Component{
    constructor(props) {
        super(props);

        this.state = {
            image: 0
        }

        this.handleNext = this.handleNext.bind(this);
        this.handlePrev = this.handlePrev.bind(this);
    }

    handleNext = (elem) => {
        const { image } = this.state;

        let newImage = elem.images[image + 1];
        if(newImage !== undefined){
            this.setState({ image: image + 1 })
        }
    }

    handlePrev = (elem) => {
        const { image } = this.state;

        let newImage = elem.images[image - 1];
        if(newImage !== undefined){
            this.setState({ image: image - 1 })
        }
    }

    render () {
        const { elem } = this.props;
        const { image } = this.state;

        let images = [];
        if(elem.images.length !== 0){
            elem.images.map(img => {
                images.push(<img src={`/annonces/thumbs/${elem.agency.dirname}/${img.thumb}`}
                                 alt={"Thumbs " + elem.label + " " + elem.address.zipcode + ", " + elem.address.city}
                                 className={"image" + (image === img.rank ? " active" : "")}
                                 key={img.rank}
                />);
            })
        }

        return <div className="gallery">
            <div className="total-images">
                <div>{images.length}</div>
                <div><span className="icon-camera" /></div>
            </div>
            <div className="images">
                {images.length !== 0 ? images : <img src={`https://robohash.org/${elem.ref}?size=150x150`} alt={`Image de ${elem.ref}`} className="image active"/>}
            </div>

            {image !== 0 && <div className="gallery-prev">
                <div className="prev" onClick={() => this.handlePrev(elem)}>
                    <span className="icon-left-chevron" />
                </div>
            </div>}

            {image !== images.length - 1 && <div className="gallery-next">
                <div className="next" onClick={() => this.handleNext(elem)}>
                    <span className="icon-right-chevron" />
                </div>
            </div>}

        </div>
    }
}