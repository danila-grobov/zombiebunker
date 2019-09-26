import React, { Component } from "react";
import $ from "jquery";
import { PLUGIN_DIR } from "../../../constants";
import Input from "./Input";
import InputEmail from "./Email";
export default class ContactInfo extends Component {
  render() {
    const {
      changeName,
      changePhone,
      changeComment,
      toggleAgree,
      enableYoung,
      disableYoung,
      phone,
      comment,
      name,
      agrees,
      young,
      sendData,
      errors,
      setErrors,
      setInputState,
      activeInputs,
      sending,
      dates,
      activeDate,
      lng,
      coupon
    } = this.props;

    const { currency } = dates[activeDate];
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
    let status = "";
    if (sending === "IDLE") status = lng === "LT" ? "Rezervuoti" : "Book";
    if (sending === "SENDING")
      status = <img src={PLUGIN_DIR + "/imgs/loading.svg"} alt="" />;
    if (sending === "ERROR")
      status = <img src={PLUGIN_DIR + "/imgs/error.svg"} alt="" />;
    if (sending === "DONE")
      status = <img src={PLUGIN_DIR + "/imgs/done.svg"} alt="" />;
    return (
      <div className="contactInfo">
        <Input
          changeFunc={changeName}
          value={name}
          setErrors={setErrors}
          errors={errors}
          name="name"
          label={lng === "LT" ? "Vardas Pavardė" : "Full Name"}
          type="TEXT"
          setInputState={setInputState}
          activeInputs={activeInputs}
        />
        <InputEmail
          name="email"
          label={lng === "LT" ? "E-pašto adresas" : "E-mail address"}
        />
        <Input
          changeFunc={changePhone}
          value={phone}
          setErrors={setErrors}
          errors={errors}
          name="phone"
          label={lng === "LT" ? "Tel. numeris" : "Phone number"}
          type="TEXT"
          setInputState={setInputState}
          activeInputs={activeInputs}
        />
        <Input
          changeFunc={changeComment}
          value={comment}
          setErrors={setErrors}
          errors={errors}
          name="comment"
          label={lng === "LT" ? "Komentaras" : "Comment"}
          type="TEXTAREA"
          setInputState={setInputState}
          activeInputs={activeInputs}
        />
        <div className="checkbox_wrapper">
          <div className="checkBox clickable" onClick={enableYoung}>
            <img
              src={
                young === true && young !== null
                  ? PLUGIN_DIR + "/imgs/checkbox_checked.svg"
                  : PLUGIN_DIR + "/imgs/checkbox_unchecked.svg"
              }
            />
            <span className="helper">
              {lng === "LT"
                ? "Žaidime dalyvaus asmenys jaunesni nei 18 metų."
                : "Persons younger than 18 years old will attend."}
            </span>
          </div>
          <div className="checkBox clickable" onClick={disableYoung}>
            <img
              src={
                young === false && young !== null
                  ? PLUGIN_DIR + "/imgs/checkbox_checked.svg"
                  : PLUGIN_DIR + "/imgs/checkbox_unchecked.svg"
              }
            />
            <span className="helper">
              {lng === "LT"
                ? "Žaidime dalyvaus 18 metų arba vyresni asmenys."
                : "18 years old or older persons will attend."}
            </span>
          </div>
          <div className="checkBox clickable">
            {errors["young"] ? (
              <div className="errorText">{"- " + errors["young"]}</div>
            ) : (
              ""
            )}
          </div>
          <div className="checkBox">
            <img
              src={
                agrees
                  ? PLUGIN_DIR + "/imgs/checkbox_checked.svg"
                  : PLUGIN_DIR + "/imgs/checkbox_unchecked.svg"
              }
              className={"clickable"}
              onClick={toggleAgree}
            />
            <span className="helper">
              {lng === "LT" ? (
                <span>
                  Sutinku su{" "}
                  <a href={"" /*THEME_URL + "/duomenu-tvarkymo-taisykles"*/}>
                    asmens duomenų tvarkymo taisyklėmis.
                  </a>
                </span>
              ) : (
                <span>
                  I accept the{" "}
                  <a href={"" /*THEME_URL + "/en/privacy-policy"*/}>
                    Privacy Policy.
                  </a>
                </span>
              )}
            </span>
          </div>
          <div className="checkBox clickable">
            {errors["agrees"] ? (
              <div className="errorText">{"- " + errors["agrees"]}</div>
            ) : (
              ""
            )}
          </div>
        </div>
        <div className="price">
          <span>
            {lng === "LT" ? "Kaina: " : "Price: "}
            <span className="bold">{price}</span>
          </span>
        </div>
        <div className="reserve_wrapper">
          <div className="reserve_button clickable" onClick={sendData}>
            {status}
          </div>
        </div>
      </div>
    );
  }
}
