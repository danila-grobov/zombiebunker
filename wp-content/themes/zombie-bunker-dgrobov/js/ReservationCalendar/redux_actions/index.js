import { THEME_URL } from "../../../constants";
import axios from "axios";
export const loadDates = (serviceId, date) => {
  return (dispatch, getState) => {
    dispatch(loadStarted());
    let loaded = 0;
    const cupon = document.cookie.match(new RegExp("(^| )coupon=([^;]+)"))
      ? document.cookie.match(new RegExp("(^| )coupon=([^;]+)"))[2]
      : "";
    let formData = new FormData();
    formData.set("couponCode", cupon);
    formData.set("serviceId", serviceId);
    axios({
      method: "post",
      url:
        THEME_URL +
        "/wp-content/plugins/booking-calendar-dgrobov/ajax/checkCoupon.php",
      data: formData,
      config: { headers: { "Content-Type": "multipart/form-data" } }
    }).then(({ data }) => {
      if (data.responce === true) dispatch(setCoupon({ ...data, code: cupon }));
      else dispatch(setCoupon(null));
      if (loaded === 1) {
        dispatch({
          type: "FINISH_LOADING"
        });
      } else loaded++;
    });
    axios
      .post(
        THEME_URL +
          "/wp-content/plugins/booking-calendar-dgrobov/api/getTimes.php",
        { date, serviceId }
      )
      .then(({ data }) => {
        dispatch({
          type: "DATES_LOADED",
          props: { dates: data, date, serviceId }
        });
        if (loaded === 1) {
          dispatch({
            type: "FINISH_LOADING"
          });
        } else loaded++;
      });
  };
};
export const sendData = () => {
  return (dispatch, getState) => {
    dispatch({
      type: "SET_SENDING",
      props: { data: "SENDING" }
    });
    const state = getState();
    const {
      serviceId,
      activeDate,
      fullName,
      email,
      comment,
      selectedTime,
      phone,
      dates,
      agrees,
      young,
      language,
      coupon
    } = state;
    const { duration } = dates[activeDate];
    let formData = new FormData();
    formData.set("date", activeDate);
    formData.set("interval", duration);
    formData.set("name", fullName);
    formData.set("email", email);
    formData.set("comments", comment);
    formData.set("time[]", selectedTime);
    formData.set("phone", phone);
    formData.set("agrees", agrees);
    formData.set("young", young);
    formData.set("lng", language);
    formData.set("couponCode", coupon !== null ? coupon.code : "");
    axios({
      method: "post",
      url:
        THEME_URL +
        "/wp-content/plugins/booking-calendar-dgrobov/booking.processing.php?serviceID=" +
        serviceId,
      data: formData,
      config: { headers: { "Content-Type": "multipart/form-data" } }
    }).then(response => {
      if (response.data.success) {
        dispatch({
          type: "SET_SENDING",
          props: { data: "DONE" }
        });
        dispatch(loadDates(serviceId, activeDate));
      } else {
        if (response.data["alert"]) {
          alert(response.data["alert"]);
          dispatch(toggleLockIn());
          dispatch(loadDates(serviceId, activeDate));
        } else {
          dispatch({
            type: "SET_SENDING",
            props: { data: "ERROR" }
          });
          dispatch(setErrors(response.data));
          setTimeout(() => {
            dispatch({ type: "SET_SENDING", props: { data: "IDLE" } });
          }, 2000);
        }
      }
    });
  };
};
export const setCoupon = coupon => ({
  type: "SET_COUPON",
  props: { coupon }
});
export const setLanguage = lng => ({
  type: "SET_LNG",
  props: { lng }
});
export const setErrors = errors => ({
  type: "SET_ERRORS",
  props: { errors }
});
export const setInputState = (name, state) => ({
  type: "SET_INPUT_STATE",
  props: { name, state }
});
export const loadStarted = () => ({
  type: "LOAD_STARTED"
});
export const toggleModal = () => ({
  type: "TOGGLE_MODAL"
});
export const toggleLockIn = () => ({
  type: "TOGGLE_LOCK_IN"
});
export const toggleAgree = () => ({
  type: "TOGGLE_AGREE"
});
export const toggleYoung = () => ({
  type: "TOGGLE_YOUNG"
});
export const changeActive = date => ({
  type: "CHANGE_ACTIVE",
  props: { date }
});
export const changeName = name => ({
  type: "CHANGE_NAME",
  props: { name }
});
export const changeEmail = email => ({
  type: "CHANGE_EMAIL",
  props: { email }
});
export const changePhone = phone => ({
  type: "CHANGE_PHONE",
  props: { phone }
});
export const changeComment = comment => ({
  type: "CHANGE_COMMENT",
  props: { comment }
});
export const changeTime = time => {
  return {
    type: "CHANGE_TIME",
    props: { time }
  };
};
