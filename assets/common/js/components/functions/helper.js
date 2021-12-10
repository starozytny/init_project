const axios = require("axios");

function processData(allText)
{
    let allTextLines = allText.split(/\r\n|\n/);
    let headers = allTextLines[0].split(';');
    let lines = [];

    for (let i=1; i<allTextLines.length; i++) {
        let data = allTextLines[i].split(';');

        lines.push({"cp": data[2], "city": data[1]});
    }

    return lines;
}

function getPostalCodes(self)
{
    axios.get( window.location.origin + "/postalcode.csv", {})
        .then(function (response) {
            self.setState({ arrayPostalCode: processData(response.data) })
        })
    ;
}

function setCityFromZipcode(self, e, arrayPostalCode)
{
    let name = e.currentTarget.name;
    let value = e.currentTarget.value;

    if(value.length <= 5){
        self.setState({ [name]: value })

        let v = ""
        if(arrayPostalCode.length !== 0){
            v = arrayPostalCode.filter(el => el.cp === value)

            if(v.length === 1){
                self.setState({ city: v[0].city })
            }
        }
    }
}

function setActive(tab, value)
{
    let active = "";
    tab.forEach(elem => {
        if(elem === value){
            active = " active";
        }
    })

    return active;
}

function createTimeHoursMinutes(hours, minutes)
{
    let date = new Date();
    date.setHours(hours); date.setMinutes(minutes);

    return date;
}

function setIncludeTimes(startHours, endHours, startMinutes, endMinutes)
{
    let includeTimes = [];
    for(let i=startHours; i <= endHours ; i++){
        for(let j=startMinutes; j <= endMinutes ; j++){
            let includeTime = new Date();
            includeTime.setHours(i); includeTime.setMinutes(j);
            includeTimes.push(includeTime)
        }
    }

    return includeTimes;
}

function extractDateToArray(date){
    if(date !== "" && date !== null){
        let string = date.toLocaleString('fr-FR').slice(0,10).replace(/-/g,'');
        string = string.split('/');


        if(string.length !== 3){
            return "";
        }

        return [parseInt(string[0]), parseInt(string[1]), parseInt(string[2])];
    }

    return "";
}

module.exports = {
    getPostalCodes,
    setCityFromZipcode,
    setActive,
    setIncludeTimes,
    createTimeHoursMinutes,
    extractDateToArray
}