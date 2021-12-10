const Helper = require('@commonComponents/functions/helper');

function setToString(hours, minutes){
    return (hours !== 0 ? (hours + "h ") : "") + (minutes !== 0 ? (minutes + "min") : "");
}

function getIntervalTime(end, start)
{
    let nInterval = ""
    if((end !== null && end !== "") && (start !== null && start !== "")){
        let timeStartHours = parseInt(start.getHours());
        let timeStartMinutes = parseInt(start.getMinutes());

        let timeEndHours = parseInt(end.getHours());
        let timeEndMinutes = parseInt(end.getMinutes());

        if(timeEndHours > timeStartHours && timeEndMinutes === 0){
            timeEndHours = timeEndHours - 1;
            timeEndMinutes = 60;
        }

        let intervalHours = timeEndHours - timeStartHours;
        let intervalMinutes = timeEndMinutes - timeStartMinutes;

        if(intervalMinutes === 60){
            intervalHours = intervalHours + 1;
            intervalMinutes = 0;
        }

        nInterval = setToString(intervalHours, intervalMinutes);
    }

    return nInterval;
}

function getHoursMinutes(duration)
{
    let timeDurationHours = 0;
    let timeDurationMinutes = 0;

    if(duration){
        let nDuration = duration.replaceAll("h", "") ;
        nDuration = nDuration.replaceAll("min", "") ;

        let timeDuration = nDuration.split(" ");

        if(timeDuration.length >= 2){
            timeDurationHours = timeDuration[0];
            timeDurationMinutes = timeDuration[1];
        }else{
            if(duration.indexOf("h") !== -1){
                timeDurationHours = timeDuration[0];
            }else if(duration.indexOf("min") !== -1){
                timeDurationMinutes = timeDuration[0];
            }
        }
    }

    return [parseInt(timeDurationHours !== "" ? timeDurationHours : 0), parseInt(timeDurationMinutes !== "" ? timeDurationMinutes : 0)];
}

function getDurationTotal(duration, duration2)
{
    let nDuration = getHoursMinutes(duration);
    let nDuration2 = getHoursMinutes(duration2);

    return setToString(nDuration[0] + nDuration2[0], nDuration[1] + nDuration2[1]);
}

/**
 * start doit être inférieur à end
 * @param startArray - array [d,m,y]
 * @param endArray - array [d,m,y]
 */
function getNbDayBetweenDate(startArray, endArray)
{
    let startYear  = startArray[2];
    let startMonth = startArray[1];
    let startDay   = startArray[0];
    let endYear    = endArray[2];
    let endMonth   = endArray[1];
    let endDay     = endArray[0];

    let nbDaysByMonth = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    let nbYears = endYear - startYear

    let days = 0;
    if(nbYears >= 0){
        if(nbYears === 0){ // même année
            if(startMonth === endMonth){ // même mois
                days = endDay - startDay;
                days = days === 0 ? 1 : days;
            }else if(startMonth < endMonth){ // classique date
                days = days + (nbDaysByMonth[startMonth] - startDay + 1); //calcul en fonction du nb de jours dans le current mois
                days = days + endDay;
                for(let i = startMonth + 1 ; i < endMonth ; i++){ //ajout des jours entre les mois exclusion mois start et end
                    days = days + nbDaysByMonth[i];
                }
            }
        }else{
            //remplir les jours du current start mois et end mois
            days = days + (nbDaysByMonth[startMonth] - startDay + 1);
            days = days + endDay;

            //remplir les mois restants du start jusqu'a la nouvelle année
            for(let i = startMonth + 1 ; i <= 12 ; i++){
                days = days + nbDaysByMonth[i];
            }
            //remplir les mois avant end mois
            for(let i = 1 ; i < endMonth ; i++){
                days = days + nbDaysByMonth[i];
            }

            //remplir les années restantes exclusion année start et end
            for(let i = startYear + 1 ; i < endYear ; i++){
                days = days + 365;
            }
        }
    }

    return days;
}

function getDurationByDay(duration, duration2, start, end)
{
    if(start !== null && start !== "" && end !== null && end !== ""){
        let nDuration = getHoursMinutes(duration);
        let nDuration2 = getHoursMinutes(duration2);

        let nbHours = nDuration[0] + nDuration2[0];
        let nbMinutes = nDuration[1] + nDuration2[1];

        let startArray = Helper.extractDateToArray(start);
        let endArray = Helper.extractDateToArray(end);

        return getNbDayBetweenDate(startArray, endArray)
    }

    return "";
}

module.exports = {
    getIntervalTime,
    getDurationTotal,
    getDurationByDay
}