import React, { Component } from "react";

class AddButton extends Component {
  state = { todo: [] };
  render() {
    const { onSaveForm } = this.props;

    return (
      <React.Fragment>
        <div className="floating-button page-fab animated bouncein delay-3">
          <a
            className="btn-floating btn-large waves-effect waves-light accent-color btn z-depth-1 modal-trigger"
            href="#modal4"
          >
            Add
          </a>
        </div>
      </React.Fragment>
    );
  }
}

export default AddButton;
