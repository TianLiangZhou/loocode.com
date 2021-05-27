import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {MetaComponent} from "./meta/meta.component";
import {ModelComponent} from "./model/model.component";

const routes: Routes = [
  {
    path: "meta",
    component: MetaComponent,
    data: {title: "元信息"}
  },
  {
    path: "model",
    component: ModelComponent,
    data: {title: "模型"}
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ExtensionRoutingModule { }
