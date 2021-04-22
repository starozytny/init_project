import React, { Component } from "react";

import LightboxOptions from "@dashboardComponents/functions/lightboxOptions";
import SimpleReactLightbox, { SRLWrapper } from "simple-react-lightbox";

export class Gallery extends Component{
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
        const { elem, isImage } = this.props;
        const { image } = this.state;

        let images = [];
        if(elem.images.length !== 0){
            elem.images.map(img => {
                let folder = "thumbs";
                let file = img.thumb;
                if(isImage){
                    folder = "images";
                    file = img.file;
                }
                images.push(<img src={`/annonces/${folder}/${elem.agency.dirname}/${file}`}
                                 alt={folder + " " + img.rank + " - " + elem.label + " " + elem.address.zipcode + ", " + elem.address.city}
                                 className={"image" + (image === img.rank ? " active" : "")}
                                 key={img.rank}
                />);
            })
        }

        return <SimpleReactLightbox>
            <SRLWrapper options={LightboxOptions.options()}>
                <div className="gallery">
                    <div className="total-images">
                        <div>{images.length}</div>
                        <div><span className="icon-camera" /></div>
                    </div>

                    <div className="images">
                        {images.length !== 0 ? images : <img src={`/immo/logos/${elem.agency.logo}`} alt={`Image de ${elem.agency.name}`}/>}
                    </div>

                    {image !== 0 && <div className="gallery-prev">
                        <div className="prev" onClick={() => this.handlePrev(elem)}>
                            <span className="icon-left-chevron" />
                        </div>
                    </div>}

                    {images.length !== 0 && image !== images.length - 1 && <div className="gallery-next">
                        <div className="next" onClick={() => this.handleNext(elem)}>
                            <span className="icon-right-chevron" />
                        </div>
                    </div>}
                </div>
            </SRLWrapper>
        </SimpleReactLightbox>
    }
}