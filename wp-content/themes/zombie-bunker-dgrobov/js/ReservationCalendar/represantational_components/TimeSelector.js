import React, { Component } from "react";
import moment from "moment";
import { MONTH_NAMES_DATE, FULL_WEEK_NAMES } from "../../../constants";
import Times from "./Times";
export default class TimeSelector extends Component {
  render() {
    const {
      activeDate,
      changeTime,
      selectedTime,
      toggleLockIn,
      dates,
      lng
    } = this.props;
    return (
      <div className="timeSelector">
        <div className="info">
          <span className="calendarInfo">
            {MONTH_NAMES_DATE[lng][moment(activeDate).month()] +
              " " +
              moment(activeDate).date()}
          </span>
          <span className="weekDay">
            {FULL_WEEK_NAMES[lng][moment(activeDate).day()]}
          </span>
          <div className="spacer"></div>
        </div>
        <Times
          times={dates[activeDate].times}
          changeTime={changeTime}
          activeTime={selectedTime}
          lng={lng}
        />
        <div
          className={
            "buttonNext " + (selectedTime !== null ? "clickable" : "disabled")
          }
          onClick={() => {
            if (selectedTime !== null) toggleLockIn();
          }}
        >
          {lng === "LT" ? "Toliau" : "Next"}
        </div>
      </div>
    );
  }
}
