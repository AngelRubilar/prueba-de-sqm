class DataModel {
    constructor(timestamp, stationName, variableName, value) {
      this.timestamp = timestamp;
      this.stationName = stationName;
      this.variableName = variableName;
      this.value = value;
    }
  
    validate() {
      if (!this.timestamp || !this.stationName || !this.variableName || this.value === undefined) {
        throw new Error('Datos incompletos');
      }
      return true;
    }
  
    toArray() {
      return [this.timestamp, this.stationName, this.variableName, this.value];
    }
  }
  
  module.exports = DataModel;