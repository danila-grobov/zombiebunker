import React, { Component } from "react";
import moment from "moment";
import uniqid from "uniqid";
export default class Times extends Component {
  render() {
    if (this.props.times.length > 0) {
      const { activeTime, changeTime } = this.props;
      const times = this.props.times.map(time => (
        <div
          className={"time clickable " + (activeTime === time ? "active" : "")}
          onClick={e => {
            changeTime(time);
          }}
          key={uniqid()}
        >
          {moment("00:00", "HH:mm")
            .minutes(time)
            .format("HH:mm")}
        </div>
      ));
      return <div className="times">{times}</div>;
    } else
      return (
        <div className="times">
          <span className="timesEmpty">
            {this.props.lng === "LT"
              ? "Laisvų vietų nėra."
              : "No more empty spaces left."}
          </span>
        </div>
      );
  }
}
