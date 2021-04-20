import React     from "react";
import Sanitize  from "@dashboardComponents/functions/sanitaze";

export function Diag({ elem }){

    let letters = ["A", "B", "C", "D", "E", "F", "G"];

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
        </div>
    </div>)
}