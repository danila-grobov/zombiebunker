import React, { Component } from "react";
import moment from "moment";
import { MONTH_NAMES_DATE, FULL_WEEK_NAMES } from "../../../constants";
export default class SessionInfo extends Component {
  render() {
    const { activeDate, dates, selectedTime, lng, coupon } = this.props;
    const { duration, currency } = dates[activeDate];
    let price = dates[activeDate].price + currency;
    if (coupon !== null) {
      if (coupon.type === "abs") {
        price = (
          <span>
            <span className="discounted">{dates[activeDate].price}</span>
            <span className="red">
              {Math.round(
                (dates[activeDate].price - parseInt(coupon.value)) * 100
              ) / 100}
              {currency}
            </span>
          </span>
        );
      }
      if (coupon.type === "rel") {
        price = (
          <span>
            <span className="discounted">{dates[activeDate].price}</span>
            <span className="red">
              {Math.round(
                (dates[activeDate].price -
                  (dates[activeDate].price * parseInt(coupon.value)) / 100) *
                  100
              ) / 100}
              {currency}
            </span>
          </span>
        );
      }
    }
    return (
      <div className="sessionInfo">
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
        <div className="timeInterval">
          {moment("00:00", "HH:mm")
            .minutes(selectedTime)
            .format("HH:mm") +
            " - " +
            moment("00:00", "HH:mm")
              .minutes(selectedTime + duration)
              .format("HH:mm")}
        </div>
        <div className="locationInfo">
          {lng === "LT"
            ? 'Į lokaciją vykstama savo transportu (šalia Nemenčinės, degalinė "EMSI"), į tikslią vietą palydi priešzombinio būrio specialas.'
            : 'You will have to arrive to the destination independently ("EMSI" gas station near Nemenčinė), you will be guided to the exact location afterwards. '}
        </div>
        <div className="price">
          <span>
            {lng === "LT" ? "Kaina: nuo " : "Price: from "}
            <span className="bold">{price}</span>
          </span>
        </div>
      </div>
    );
  }
}
