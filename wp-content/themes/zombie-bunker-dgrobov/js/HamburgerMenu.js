import React, { Component, Fragment, createRef } from "react";
import { PLUGIN_DIR } from "../constants";
import $ from "jquery";
import uniqid from "uniqid";
export default class HamburgerMenu extends Component {
  constructor(props) {
    super(props);
    this.menu = createRef();
  }
  state = {
    display: "none"
  };
  hideMenu = () => {
    this.setState({ display: "none" });
  };
  render() {
    const elementArray = $(".menu-container li");
    const elements = elementArray.map((i, e) => {
      const innerElement = $(e).children("a")[0];
      return (
        <Fragment key={uniqid()}>
          <span className={e.className} key={uniqid()}>
            <a onClick={this.hideMenu} href={innerElement.href}>
              {innerElement.innerText}
            </a>
          </span>
          {elementArray.length - 1 == i ? (
            ""
          ) : (
            <div className="spacer" key={uniqid()} />
          )}
        </Fragment>
      );
    });
    return (
      <Fragment>
        <img
          src={this.props.src}
          className="menuIcon"
          onClick={() => {
            this.setState({ display: "grid" });
          }}
        />
        <div className="navMenu" ref={this.menu} style={this.state}>
          <div className="topBar">
            <img
              src={PLUGIN_DIR + "/imgs/close_red.svg"}
              className="closeButton"
              onClick={() => this.hideMenu()}
            />
          </div>
          <div className="contentWrapper">{elements}</div>
        </div>
      </Fragment>
    );
  }
}
