import React, { Component } from "react";
import { PLUGIN_DIR, MONTH_NAMES, WEEK_NAMES } from "../../../constants";
import moment from "moment";
import "moment-isocalendar";
import uniqid from "uniqid";
export default class Calendar extends Component {
  getDayArray = (weekNumber, initDate) => {
    const { dates, change } = this.props;
    let days = [[], [], [], [], [], [], []];
    for (let week = 0; week < 6; week++) {
      for (let weekday = 0; weekday < 7; weekday++) {
        const date = moment.fromIsocalendar([
          initDate.year(),
          weekNumber + week,
          weekday
        ]);
        days[weekday].push(
          <div
            className={
              "day " +
              (date.month() === initDate.month() ? "clickable " : "disabled ")
            }
            onClick={() => {
              if (date.month() === initDate.month())
                change(date.format("YYYY-MM-DD"));
            }}
            key={uniqid()}
          >
            <span
              className={
                "date " +
                (date.format("YYYY-MM-DD") == initDate.format("YYYY-MM-DD")
                  ? "active"
                  : "")
              }
            >
              {date.date()}
            </span>
            {date.format("YYYY-MM-DD") == moment().format("YYYY-MM-DD") ? (
              <div className="today"></div>
            ) : (
              ""
            )}
            {date.month() === initDate.month() ? (
              <div className="spacesLeft">
                {dates[date.format("YYYY-MM-DD")].times.length > 0
                  ? dates[date.format("YYYY-MM-DD")].times.length
                  : ""}
              </div>
            ) : (
              ""
            )}
          </div>
        );
      }
    }
    return days;
  };
  render() {
    const { loadDates, serviceId, lng } = this.props;

    const date = moment(this.props.date);
    const weekNumber = moment(
      [date.year(), date.month() + 1, 1],
      "YYYY-MM-DD"
    ).week();
    const days = this.getDayArray(weekNumber, date);
    let weeks = [];
    for (let weekday = 0; weekday < 7; weekday++) {
      weeks.push(
        <div className="column" key={uniqid()}>
          <span className="weekDay">{WEEK_NAMES[lng][weekday]}</span>
          <div className="days">{days[weekday]}</div>
        </div>
      );
    }
    return (
      <div className="calendar">
        <div className="title">
          <div className="wrapper">
            <img
              src={PLUGIN_DIR + "/imgs/left.svg"}
              className="left clickable"
              onClick={() =>
                loadDates(
                  serviceId,
                  date
                    .subtract("month", 1)
                    .set("date", 1)
                    .format("YYYY-MM-DD")
                )
              }
            />
            <span className="monthName">
              {MONTH_NAMES[lng][date.month()] + " " + date.year()}
            </span>
            <img
              src={PLUGIN_DIR + "/imgs/right.svg"}
              className="right clickable"
              onClick={() =>
                loadDates(
                  serviceId,
                  date
                    .add(1, "month")
                    .set("date", 1)
                    .format("YYYY-MM-DD")
                )
              }
            />
          </div>
        </div>
        <div className="content">{weeks}</div>
      </div>
    );
  }
}
