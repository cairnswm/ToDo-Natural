import React, { Component } from "react";
import "../css/style.css";

class Header extends Component {
  state = {};
  render() {
    return (
      <React.Fragment>
        <div id="toolbar" className="halo-nav box-shad-none">
          <div
            className="open-left"
            id="open-left"
            data-activates="slide-out-left"
          >
            <i className="ion-android-menu" style={{ color: "#005599" }} />
          </div>
          <span className="title none">Tour Divide - To Dos</span>
          <div
            className="open-right"
            id="open-right"
            data-activates="slide-out"
          >
            <i className="ion-android-person" style={{ color: "#005599" }} />
          </div>
        </div>

        <header>
          <div className="h-banner animated fadeindown">
            <div className="parallax bgtodo">
              <div className="banner-title" style={{ color: "#005599" }}>
                To Do
              </div>
            </div>
          </div>
        </header>
      </React.Fragment>
    );
  }
}

export default Header;
