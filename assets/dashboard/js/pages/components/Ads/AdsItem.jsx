import React, { Component } from 'react';


export class AdsItem extends Component {
    render () {
        const { elem, onChangeContext, onDelete, onSelectors } = this.props

        return <div className="item">
            <div className="selector">
                <div>#{elem.id}</div>
            </div>

            <div className="item-content">
                <div className="item-body">
                    <div className="avatar">
                        {elem.thumb ? <img src={`/annonces/thumbs/${elem.agency.dirname}/${elem.thumb}`} alt={`Image de ${elem.ref}`}/> : <img src={`https://robohash.org/${elem.ref}?size=150x150`} alt={`Image de ${elem.ref}`}/>}
                    </div>
                    <div className="infos">
                        <div>
                            <div className="name">
                                <div>{elem.label}</div>
                            </div>
                            <div className="sub sub-username">{elem.address.zipcode}, {elem.address.city}</div>
                            <div className="sub sub-username">{elem.agency.name}</div>
                            <div className="sub">{elem.ref} - {elem.realRef}</div>
                        </div>
                        <div>
                            <div className="role">{elem.typeAd}</div>
                        </div>
                        <div>
                            <div className="role">{elem.typeBien}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}