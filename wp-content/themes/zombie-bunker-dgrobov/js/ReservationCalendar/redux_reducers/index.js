import moment from "moment";
import dotProp from "dot-prop-immutable";
import $ from "jquery";
import React from "react";
const initialState = {
  activeDate: moment().format("YYYY-MM-DD"),
  selectedTime: null,
  selectedInterval: null,
  lockedIn: false,
  loading: true,
  dates: {},
  fullName: "",
  email: "",
  phone: "",
  comment: "",
  agrees: false,
  young: false,
  serviceId: null,
  open: false,
  sending: "IDLE",
  errors: [],
  activeInputs: [],
  countries: [],
  selectedCountry: {
    value: "Lithuania",
    label: <img src={"https://restcountries.eu/data/ltu.svg"} />
  },
  language: "LT",
  coupon: null
};

export default (state = initialState, { type, props }) => {
  switch (type) {
    case "SET_ERRORS": {
      return dotProp.set(state, "errors", props.errors);
    }
    case "SET_LNG": {
      return dotProp.set(state, "language", props.lng);
    }
    case "SET_SELECTED_COUNTRY": {
      return dotProp.set(state, "selectedCountry", props.name);
    }
    case "SET_COUNTRIES": {
      return dotProp.set(state, "countries", props.countries);
    }
    case "SET_COUPON": {
      return dotProp.set(state, "coupon", props.coupon);
    }
    case "SET_INPUT_STATE": {
      const index = state.activeInputs.findIndex(
        input => input.name == props.name
      );
      if (index !== -1)
        return dotProp.set(state, "activeInputs." + index, {
          state: props.state,
          name: props.name
        });

      return dotProp.set(state, "activeInputs", list => [
        ...list,
        {
          state: props.state,
          name: props.name
        }
      ]);
    }
    case "DATES_LOADED": {
      const { dates, date, serviceId } = props;
      const stateWithNewDate = dotProp.set(state, "activeDate", date);
      const stateWithDates = dotProp.set(stateWithNewDate, "dates", dates);
      return dotProp.set(stateWithDates, "serviceId", serviceId);
    }
    case "FINISH_LOADING": {
      return dotProp.set(state, "loading", false);
    }
    case "LOAD_STARTED": {
      return dotProp.set(state, "loading", true);
    }
    case "SET_SENDING": {
      return dotProp.set(state, "sending", props.data);
    }
    case "TOGGLE_MODAL": {
      $("body").toggleClass("modal-open");
      if (state.open === true) return initialState;
      return dotProp.toggle(state, "open");
    }
    case "CHANGE_ACTIVE": {
      const { date } = props;
      const stateWithTime = dotProp.set(state, "selectedTime", null);
      return dotProp.set(stateWithTime, "activeDate", date);
    }
    case "CHANGE_PHONE": {
      const { phone } = props;
      return dotProp.set(state, "phone", phone);
    }
    case "CHANGE_EMAIL": {
      const { email } = props;
      return dotProp.set(state, "email", email);
    }
    case "CHANGE_NAME": {
      const { name } = props;
      return dotProp.set(state, "fullName", name);
    }
    case "CHANGE_COMMENT": {
      const { comment } = props;
      return dotProp.set(state, "comment", comment);
    }
    case "TOGGLE_AGREE": {
      return dotProp.toggle(state, "agrees");
    }
    case "TOGGLE_YOUNG": {
      return dotProp.toggle(state, "young");
    }
    case "CHANGE_TIME": {
      const { time } = props;
      return dotProp.set(state, "selectedTime", time);
    }
    case "TOGGLE_LOCK_IN": {
      const changedSending = dotProp.set(state, "sending", "IDLE");
      $(".modal")[0].scrollTop = 0;
      return dotProp.toggle(changedSending, "lockedIn");
    }
    default:
      return state;
  }
};
