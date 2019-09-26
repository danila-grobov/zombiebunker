import React, { Component } from "react";
import { PLUGIN_DIR, MONTH_NAMES, WEEK_NAMES } from "../../../constants";
import moment from "moment";
import "moment-isocalendar";
import uniqid from "uniqid";
export default class Calendar extends Component {
  getWeeks = (weekNumber, initDate) => {
    let weeks = [];
    const { dates, change } = this.props;
    let days = [[], [], [], [], [], [], []];
    for (let week = 0; week < 7; week++) {
      for (let weekday = 0; weekday < 7; weekday++) {
        weeks.push(
          moment.fromIsocalendar([initDate.year(), weekNumber + week, weekday])
        );
      }
    }
    let counter = 0;
    for (let day = 1; day < 43; day++) {
      const date = weeks[day];
      if (counter == 7) {
        counter = 0;
      }
      days[counter].push(
        // date.format("YYYY-MM-DD")
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
      counter++;
    }
    return days;
  };
  render() {
    const { loadDates, serviceId, lng } = this.props;
    const date = moment(this.props.date);
    const weekDayPos = [1, 2, 3, 4, 5, 6, 0];
    const weekNumber = moment(
      [date.year(), date.month() + 1, 1],
      "YYYY-MM-DD"
    ).isoWeek();
    const days = this.getWeeks(weekNumber, date);
    let weeks = [];
    for (let weekday = 0; weekday < 7; weekday++) {
      weeks.push(
        <div className="column" key={uniqid()}>
          <span className="weekDay">
            {WEEK_NAMES[lng][weekDayPos[weekday]]}
          </span>
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
