import React     from "react";
import Sanitize  from "@commonComponents/functions/sanitaze";

export function Features({ elem }){
    return (<div className="details-tab-infos">

        <div className="details-tab-infos-main">
            <div>
                <div className="label">Nombre de pièces</div>
                <div>{elem.feature.nbPiece}</div>
            </div>
            <div>
                <div className="label">Nombre de chambres</div>
                <div>{elem.feature.nbRoom}</div>
            </div>
            <div>
                <div className="label">Nombre de balcon</div>
                <div>{elem.feature.nbBalcony}</div>
            </div>
            <div>
                <div className="label">Année de construction</div>
                <div>{elem.feature.constructionYear}</div>
            </div>
            <div>
                <div className="label">Refait à neuf</div>
                <div>{Sanitize.getTrilieanResponse(elem.feature.isRefaitNeuf, false)}</div>
            </div>
        </div>

        <div className="details-tab-infos-main">
            <div>
                <div className="label">Surface</div>
                <div>{elem.feature.area} m²</div>
            </div>
            <div>
                <div className="label">Surface terrain</div>
                <div>{elem.feature.areaLand} m²</div>
            </div>
            <div>
                <div className="label">Surface séjour</div>
                <div>{elem.feature.areaLiving} m²</div>
            </div>
        </div>

        <div className="details-tab-infos-main">
            <div>
                <div className="label">Nombre de salle de bain</div>
                <div>{elem.feature.nbSdb}</div>
            </div>
            <div>
                <div className="label">Nombre de salle d'eau</div>
                <div>{elem.feature.nbSle}</div>
            </div>
            <div>
                <div className="label">Nombre de WC</div>
                <div>{elem.feature.nbWc}</div>
            </div>
            <div>
                <div className="label">WC séparé</div>
                <div>{Sanitize.getTrilieanResponse(elem.feature.isWcSepare, false)}</div>
            </div>
        </div>

        <div className="details-tab-infos-main">
            <div>
                <div className="label">Cuisine</div>
                <div>{elem.feature.kitchen}</div>
            </div>
            <div>
                <div className="label">Chauffage</div>
                <div>{elem.feature.heating}</div>
            </div>
        </div>

        <div className="details-tab-infos-main">
            <div>
                <div className="label">Nombre d'étages</div>
                <div>{elem.feature.nbFloor}</div>
            </div>
            <div>
                <div className="label">Etage</div>
                <div>{elem.feature.floor}</div>
            </div>
        </div>

        <div className="details-tab-infos-main">
            <div>
                <div className="label">Exposition Nord</div>
                <div>{Sanitize.getTrilieanResponse(elem.feature.isNorth, false)}</div>
            </div>
            <div>
                <div className="label">Exposition Est</div>
                <div>{Sanitize.getTrilieanResponse(elem.feature.isEast, false)}</div>
            </div>
            <div>
                <div className="label">Exposition Sud</div>
                <div>{Sanitize.getTrilieanResponse(elem.feature.isSouth, false)}</div>
            </div>
            <div>
                <div className="label">Exposition Ouest</div>
                <div>{Sanitize.getTrilieanResponse(elem.feature.isWest, false)}</div>
            </div>
        </div>

        {elem.featureExt && <>
            <div className="details-tab-infos-content">
                <div className="label">Commodités</div>
            </div>

            <div className="details-tab-infos-main">
                <div>
                    <div className="label">Nombre de parkings</div>
                    <div>{elem.featureExt.nbParking}</div>
                </div>
                <div>
                    <div className="label">Nombre de box</div>
                    <div>{elem.featureExt.nbBox}</div>
                </div>
            </div>

            <div className="details-tab-infos-main">
                <div>
                    <div className="label">Ascenseur</div>
                    <div>{Sanitize.getTrilieanResponse(elem.featureExt.hasElevator, false)}</div>
                </div>
                <div>
                    <div className="label">Cave</div>
                    <div>{Sanitize.getTrilieanResponse(elem.featureExt.hasCellar, false)}</div>
                </div>
                <div>
                    <div className="label">Interphone</div>
                    <div>{Sanitize.getTrilieanResponse(elem.featureExt.hasIntercom, false)}</div>
                </div>
                <div>
                    <div className="label">Gardiennage</div>
                    <div>{Sanitize.getTrilieanResponse(elem.featureExt.hasConcierge, false)}</div>
                </div>
                <div>
                    <div className="label">Terrasse</div>
                    <div>{Sanitize.getTrilieanResponse(elem.featureExt.hasTerrace, false)}</div>
                </div>
                <div>
                    <div className="label">Climatisation</div>
                    <div>{Sanitize.getTrilieanResponse(elem.featureExt.hasClim, false)}</div>
                </div>
                <div>
                    <div className="label">Piscine</div>
                    <div>{Sanitize.getTrilieanResponse(elem.featureExt.hasPiscine, false)}</div>
                </div>
            </div>
        </>}

    </div>)
}