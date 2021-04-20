import React     from "react";
import Sanitize  from "@dashboardComponents/functions/sanitaze";

export function Diag({ elem }){

    let letters = ["A", "B", "C", "D", "E", "F", "G"];
    let lettersDetails = [
        { le :"A", valDpe: "≤ 50", valGes: "≤ 5" },
        { le :"B", valDpe: "51 à 90", valGes: "6 à 10" },
        { le :"C", valDpe: "91 à 150", valGes: "11 à 20" },
        { le :"D", valDpe: "151 à 230", valGes: "21 à 35" },
        { le :"E", valDpe: "231 à 330", valGes: "36 à 55" },
        { le :"F", valDpe: "331 à 450", valGes: "56 à 80" },
        { le :"G", valDpe: "> 450", valGes: "> 80" }
    ]

    return (<div className="details-tab-infos">

        <div className="details-tab-infos-main">
            <div className="diagnostic">
                <div>
                    <div className="diag-title">Diagnostic de performance énergétique</div>
                    <div className="diag-dpe">
                        {letters.map(le => {
                            return <div key={le}>
                                <div className={"dpe dpe-" + le.toLowerCase()}>
                                    <div>{le}</div>
                                </div>
                                <div className="number">5</div>
                            </div>
                        })}
                    </div>
                </div>
            </div>
            <div className="diagnostic-details">
                <div>
                    <div className="diag-title">Diagnostic de performance énergétique En kWhEP/m2.an</div>
                    <div className="diag-dpe">
                        {lettersDetails.map(le => {
                            return <div key={le.le}>
                                <div className={"dpe dpe-" + le.le.toLowerCase()}>
                                    <div>{le.valDpe}</div>
                                    <div>{le.le}</div>
                                </div>
                                <div className="number">5</div>
                            </div>
                        })}
                    </div>
                </div>
            </div>
        </div>

        <div className="details-tab-infos-main">
            <div className="diagnostic">
                <div>
                    <div className="diag-title">Indice d'émission de gaz à effet de serre</div>
                    <div className="diag-ges">
                        {letters.map(le => {
                            return <div key={le}>
                                <div className={"ges ges-" + le.toLowerCase()}>
                                    <div>{le}</div>
                                </div>
                                <div className="number">5</div>
                            </div>
                        })}
                    </div>
                </div>
            </div>
            <div className="diagnostic-details">
                <div>
                    <div className="diag-title">Indice d'émission de gaz à effet de serre En kWhEP/m2.an</div>
                    <div className="diag-ges">
                        {lettersDetails.map(le => {
                            return <div key={le.le}>
                                <div className={"ges ges-" + le.le.toLowerCase()}>
                                    <div>{le.valGes}</div>
                                    <div>{le.le}</div>
                                </div>
                                <div className="number">5</div>
                            </div>
                        })}
                    </div>
                </div>
            </div>
        </div>
    </div>)
}