import React, { Component, createRef } from "react";
import PhoneNumber from "awesome-phonenumber";
import InputMask from "react-input-mask";
export default class Input extends Component {
  constructor(props) {
    super(props);
    this.email = createRef();
  }
  render() {
    const {
      type,
      name,
      setErrors,
      errors,
      changeFunc,
      label,
      setInputState,
      activeInputs
    } = this.props;
    let className = "";
    let inputType = "text";
    let value = this.props.value;
    let mask = "";
    if (name === "phone") {
      const regionCode = PhoneNumber(value).getRegionCode();
      if (regionCode && regionCode !== null) {
        const exampleNumber = PhoneNumber.getExample(
          regionCode,
          "mobile"
        ).getNumber("international");
        mask = exampleNumber.replace(/[0-9]/g, "9");
      }
    }
    if (name === "phone") inputType = "tel";
    if (name === "email") inputType = "email";
    if (errors[name]) className += "error ";
    if (
      activeInputs.find(input => input.name == name) &&
      activeInputs.find(input => input.name == name).state
    )
      className += "active ";
    if (type === "TEXT")
      return (
        <div className="input">
          <InputMask
            type={inputType}
            id={name}
            ref={this[name]}
            mask={mask}
            className={className}
            onClick={() => setInputState(name, true)}
            onBlur={e => {
              if (e.currentTarget.value === "") setInputState(name, false);
            }}
            name={name === "phone" ? "country-code" : name}
            autoComplete={name === "phone" ? "tel" : name}
            onChange={e => {
              setInputState(name, true);
              let errorTemp = { ...errors };
              delete errorTemp[name];
              setErrors(errorTemp);
              changeFunc(e.currentTarget.value);
            }}
            value={value}
          />
          <label htmlFor={name}>{label}</label>
          {errors[name] ? (
            <div className="errorText">{"- " + errors[name]}</div>
          ) : (
            ""
          )}
        </div>
      );
    if (type === "TEXTAREA")
      return (
        <div className="input">
          <textarea
            id={name}
            rows="3"
            className={className}
            onClick={() => setInputState(name, true)}
            onBlur={e => {
              if (e.currentTarget.value === "") setInputState(name, false);
            }}
            onChange={e => {
              let errorTemp = { ...errors };
              delete errorTemp[name];
              setErrors(errorTemp);
              changeFunc(e.currentTarget.value);
            }}
            value={value}
          ></textarea>
          <label htmlFor={name}>{label}</label>
          {errors[name] ? (
            <div className="errorText">{"- " + errors[name]}</div>
          ) : (
            ""
          )}
        </div>
      );
  }
}
